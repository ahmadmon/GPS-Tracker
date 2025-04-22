<?php

namespace App\Http\Controllers\Wallet;

use App\Enums\Wallet\TransactionStatus;
use App\Enums\Wallet\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\WalletChargeRequest;
use App\Http\Services\Payment\PaymentService;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;

class WalletManagementController extends Controller
{
    public function show(Wallet $wallet)
    {
        $transactions = $wallet->transactions()->latest()->cursor();
        $wallet->load('walletable');
        $walletable = $wallet->walletable;


        return view('wallet.show', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'isUser' => $walletable instanceof User,
            'entity' => $walletable,
        ]);
    }

    /**
     * @param Request $request
     * @param Wallet $wallet
     */
    public function filter(Request $request, Wallet $wallet)
    {
        $type = $request->input('type');
        $status = $request->input('status');
        $date = !is_null($request->input('date')) ? Jalalian::fromFormat('Y-m-d', $request->input('date'))->toCarbon() : null;

        $wallet->load('transactions');
        $walletable = $wallet->walletable;

        $transactions = $wallet->transactions()
            ->when(isset($type), fn($q) => $q->where('type', $type))
            ->when(isset($status), fn($q) => $q->where('status', $status))
            ->when(isset($date), fn($q) => $q->whereDate('created_at', $date))
            ->latest()
            ->cursor();


        return view('wallet.show', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'isUser' => $walletable instanceof User,
            'entity' => $walletable,
            'hasFilters' => $this->hasFilters($request)
        ]);
    }


    public function create(Wallet $wallet)
    {
        $walletable = $wallet->walletable;

        return view('wallet.create', [
            'wallet' => $wallet->load('walletable'),
            'entity' => $walletable,
            'isUser' => $walletable instanceof User
        ]);
    }

    public function store(Wallet $wallet, WalletChargeRequest $request)
    {
        $inputs = $request->validated();

        $this->createTransaction($inputs, $wallet, false);

        $wallet->increment('balance', (int)$inputs['amount']);
        return to_route('wallet-management.show', $wallet)->with('success-alert', "💳 عملیات شارژ کیف پول با موفقیت تکمیل شد.");
    }

    public function sendToGateway(Wallet $wallet, WalletChargeRequest $request, PaymentService $paymentService)
    {
        $inputs = $request->validated();

        // Creating transaction and payment record
        $transaction = $this->createTransaction($inputs, $wallet);
        $payment = $this->createPayment($transaction, $inputs['amount']);

        try {
            // Transfer to payment gateway
            $paymentGateway = $paymentService->paymentPage($transaction, $payment);
            return redirect()->away($paymentGateway);

        } catch (\Exception $e) {
            Log::error('payment failed', [$e->getMessage()]);

            return to_route('profile.wallet')->with('error-alert', "خطا در اتصال به درگاه پرداخت.\n لطفاً چند دقیقه دیگر مجدداً تلاش نمایید.");
        }
    }

    public function retryPayment(string $walletId, string $transactionNumber, PaymentService $paymentService)
    {
        $transaction = WalletTransaction::with('payment')->where('transaction_number', $transactionNumber)->first();

        // Check status
        if (!$transaction->status->isPending() || (!$transaction->payment || !$transaction->payment->status->isPending())) {
            return to_route('profile.wallet')->with('error-alert', 'این تراکنش دیگر قابل پرداخت نیست.');
        }
        $transaction->update(['created_at' => now()]);

        try {
            $gatewayURL = $paymentService->paymentPage($transaction, $transaction->payment);
            return redirect()->away($gatewayURL);

        } catch (\Exception $e) {
            Log::error('Retry Payment Failed', ['message' => $e->getMessage()]);
            return to_route('profile.wallet')->with('error-alert', 'اتصال مجدد به درگاه با خطا مواجه شد. لطفاً بعداً تلاش کنید.');
        }
    }

    public function changeTransactionStatus(Wallet $wallet, WalletTransaction $transaction, Request $request)
    {
        $input = $request->validate(['trx-status' => 'required|string|in:success,failed']);

        // Check status
        if (!$transaction->status->isPending() || (!$transaction->payment || !$transaction->payment->status->isPending())) {
            return to_route('wallet-management.show', $wallet->id)->with('error-alert', 'این تراکنش دیگر قابل پرداخت نیست.');
        }

        if ($input['trx-status'] === TransactionStatus::SUCCESS->value) {
            $wallet->increment('balance', (int)$transaction->amount);
        }

        $transaction->update([
            'status' => $input['trx-status'],
            'created_at' => Carbon::now()
        ]);
        $alertMessage = $input['trx-status'] === 'success' ? 'تراکنش با موفقیت تایید گردید و مبلغ به موجودی کیف پول افزوده شد.' : 'تراکنش با موفقیت لغو شد.';
        return to_route('wallet-management.show', $wallet->id)->with('success-alert', $alertMessage);
    }

    /*
    |--------------------------------------------------------------------------
    | Api Functions
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * @param Wallet $wallet
     * @param WalletTransaction $transaction
     * @return JsonResponse
     */
    public function getTransaction(Wallet $wallet, WalletTransaction $transaction)
    {
        return response()->json([
            'transaction' => $transaction,
            'url' => route('wallet-management.change-transaction-status', [$wallet->id, $transaction->transaction_number]),
            'gatewayUrl' => route('wallet-management.retry-payment-gateway', [$wallet->id, $transaction->transaction_number])
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helper Functions
    |--------------------------------------------------------------------------
    |
    |
    */

    /**
     * @param Request $request
     * @return bool
     */
    private function hasFilters(Request $request): bool
    {
        return $request->get('type') || $request->get('status') || $request->get('date');
    }

    private function createTransaction($inputs, Wallet $wallet, bool $isOnlinePayment = true)
    {
        return $wallet->transactions()->create([
            'type' => 'credit',
            'status' => $isOnlinePayment ? 'pending' : 'success',
            'amount' => $inputs['amount'],
            'description' => $inputs['description'] ?? null,
        ]);
    }

    private function createPayment(WalletTransaction $transaction, int $amount)
    {
        return $transaction->payment()->create([
            'amount' => $amount,
            'gateway' => 'زرین پال',
            'status' => 'pending'
        ]);
    }

    /**
     * @param int|null $amount
     * @param array|null $verifyResponse
     * @param int|null $balance
     * @param string|null $type
     * @return string
     */
    private function successMessage(?int $amount = null, ?array $verifyResponse = null, ?int $balance = null, ?string $type = null): string
    {
        if ($type) {
            return $type === TransactionType::isCredit() ? "💳 عملیات شارژ کیف پول با موفقیت تکمیل شد." : "عملیات برداشت از کیف پول با موفقیت تکمیل شد.";
        }

        return sprintf(
            "💳 عملیات شارژ کیف پول با موفقیت تکمیل شد.\n\n" .
            "✳️ جزئیات تراکنش:\n" .
            "▫️ مبلغ: %s تومان\n" .
            "▫️ کد رهگیری: %s\n" .
            "▫️ زمان: %s \n\n" .
            "💰 موجودی فعلی: %s تومان\n\n" .
            "در صورت هرگونه مشکل با پشتیبانی تماس بگیرید.",
            priceFormat($amount),
            $verifyResponse['referenceId'],
            jalaliDate($verifyResponse['date'], format: '%d %B %Y, H:i') ?? jalaliDate(now(), format: '%d %B %Y, H:i'),
            priceFormat($balance)
        );
    }

}
