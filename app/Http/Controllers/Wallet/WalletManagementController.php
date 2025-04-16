<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\WalletChargeRequest;
use App\Http\Services\Payment\PaymentService;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;

class WalletManagementController extends Controller
{
    public function show(Wallet $wallet)
    {
        $transactions = $wallet->transactions()->latest()->cursor();

        return view('wallet.user.show', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'user' => $wallet->walletable,
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

        $transactions = $wallet->transactions()
            ->when(isset($type), fn($q) => $q->where('type', $type))
            ->when(isset($status), fn($q) => $q->where('status', $status))
            ->when(isset($date), fn($q) => $q->whereDate('created_at', $date))
            ->latest()
            ->cursor();


        return view('wallet.user.show', [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'user' => $wallet->walletable,
            'hasFilters' => $this->hasFilters($request)
        ]);
    }


    public function create(Wallet $wallet)
    {
        return view('wallet.create', [
            'walletId' => $wallet->id,
            'type' => $wallet->walletable_type === User::class ? 'کاربر' : 'سازمان'
        ]);
    }

    public function store(Wallet $wallet, WalletChargeRequest $request)
    {
        $inputs = $request->validated();

        $transaction = $this->createTransaction($inputs, $wallet, false);

        $wallet->increment('balance', (int)$inputs['amount']);
        return to_route('wallet-management.show', $wallet)->with('success-alert', "خطا در اتصال به درگاه پرداخت.\n لطفاً چند دقیقه دیگر مجدداً تلاش نمایید.");
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
            'type' => $inputs['type'],
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

}
