<?php

namespace App\Livewire\Wallet;

use App\Http\Services\Payment\PaymentService;
use App\Models\Company;
use App\Models\Payment;
use App\Models\User;
use App\Models\WalletTransaction;
use Exception;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use LaravelIdea\Helper\App\Models\_IH_WalletTransaction_C;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class WalletPage extends Component
{
    #[Validate('required|numeric|min:10000|max:50000000')]
    public string $amount = "10,000";
    #[Validate('nullable|string|min:5')]
    public null|string $description = null;

    public function mount()
    {
        if (!auth()->user()->wallet) to_route('home');
    }

    #[Title('کیف پول من')]
    public function render()
    {
        $user = Auth::user();


        return view('livewire.wallet.wallet-page', [
            'user' => $user,
            'wallet' => $user->wallet,
            'myTransactions' => $this->myTransactions(),
            'companiesTransactions' => $this->companiesTransactions()
        ]);
    }


    /**
     * @param PaymentService $paymentService
     */
    public function handleWallet(PaymentService $paymentService)
    {
        try {

            $this->amount = str_replace(',', '', $this->amount);
            $this->validate();

            $transaction = $this->createTransaction();
            $payment = $this->createPayment($transaction);

            // Transfer to payment gateway
            $paymentGateway = $paymentService->paymentPage($transaction, $payment);
            return redirect()->away($paymentGateway);

        } catch (Exception $e) {
            Log::error('payment failed', [$e->getMessage()]);

            return to_route('profile.wallet')->with('error-alert', "خطا در اتصال به درگاه پرداخت.\n لطفاً چند دقیقه دیگر مجدداً تلاش نمایید.");
        }
    }

    /**
     * @param Request $request
     * @param WalletTransaction $transaction
     * @param Payment $payment
     * @param PaymentService $paymentService
     * @return RedirectResponse|void
     */
    public function paymentCallback(Request $request, WalletTransaction $transaction, Payment $payment, PaymentService $paymentService)
    {
        try {
            $payment->update(['bank_first_response' => json_encode($request->all())]);

            $verifyResponse = $paymentService->paymentVerify((int)$transaction->amount, $payment);
            if (strtoupper($request->Status) === 'OK') {

                $payment->update([
                    'status' => 'success',
                    'bank_second_response' => json_encode($verifyResponse)
                ]);

                $transaction->update(['status' => 'success']);

                $wallet = $transaction->wallet;
                $wallet->increment('balance', (int)$transaction->amount);

                return to_route('profile.wallet')->with('success-alert', $this->successMessage($transaction->amount, $verifyResponse, $wallet->balance));

            } else {
                if ($verifyResponse) {
                    $payment->update([
                        'status' => 'failed',
                        'bank_second_response' => is_string($verifyResponse) ? $verifyResponse : json_encode($verifyResponse)
                    ]);

                    $transaction->update(['status' => 'failed']);

                    return to_route('profile.wallet')->with('error-alert', $this->failedMessage($transaction->amount, $verifyResponse));
                }
            }
        } catch (Exception $e) {
            Log::error('payment failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return to_route('profile.wallet')->with('error-alert',
                "❌ مشکلی در پردازش پرداخت به وجود آمد.\nدر صورت کسر مبلغ، وجه تا ۷۲ ساعت آینده به حساب شما بازگردانده خواهد شد.\nلطفاً در صورت نیاز با پشتیبانی تماس بگیرید."
            );
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
     * @return CursorPaginator
     */
    private function myTransactions(): CursorPaginator
    {
        return WalletTransaction::where('source_id', auth()->id())
            ->where('source_type', User::class)
            ->orderByDesc('created_at')
            ->limit(7)
            ->cursorPaginate();
    }

    /**
     * @return CursorPaginator
     */
    private function companiesTransactions(): CursorPaginator
    {
        return WalletTransaction::where('source_id', auth()->id())
            ->where('source_type', Company::class)
            ->with('source')
            ->orderByDesc('created_at')
            ->limit(7)
            ->cursorPaginate();
    }

    private function createTransaction()
    {
        return Auth::user()->wallet->transactions()->create([
            'type' => 'credit',
            'status' => 'pending',
            'amount' => (int)$this->amount,
            'description' => $this?->description ?? null,
            'source_type' => Auth::user()::class,
            'source_id' => Auth::id()
        ]);
    }

    private function createPayment(WalletTransaction $transaction)
    {
        return $transaction->payment()->create([
            'amount' => (int)$this->amount,
            'gateway' => 'saman',
            'status' => 'pending'
        ]);
    }

    /**
     * @param int $amount
     * @param array $verifyResponse
     * @param $balance
     * @return string
     */
    private function successMessage(int $amount, array $verifyResponse, $balance): string
    {
        return sprintf(
            "💳 عملیات شارژ کیف پول با موفقیت تکمیل شد\n\n" .
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

    /**
     * @param int $amount
     * @param array|string|null $verifyResponse
     * @return string
     */
    private function failedMessage(int $amount, array|string|null $verifyResponse = null): string
    {
        $referenceId = is_array($verifyResponse) ? $verifyResponse['referenceId'] ?? '---' : '---';
        $date = is_array($verifyResponse) ? jalaliDate($verifyResponse['date'], format: '%d %B %Y, H:i') ?? jalaliDate(now(), format: '%d %B %Y, H:i') : jalaliDate(now(), format: '%d %B %Y, H:i');
        $errorMessage = is_string($verifyResponse) ? $verifyResponse : null;

        return sprintf(
            "❌ عملیات پرداخت ناموفق بود\n\n" .
            "✳️ جزئیات تراکنش:\n" .
            "▫️ مبلغ: %s تومان\n" .
            "▫️ کد رهگیری: %s\n" .
            "▫️ زمان: %s\n\n" .
            "%s\n" .
            "💡 در صورت کسر وجه، مبلغ طی ۷۲ ساعت آینده به کارت بانکی شما بازگردانده خواهد شد.",
            priceFormat($amount),
            $referenceId,
            $date,
            $errorMessage ? "🛑 توضیح خطا: " . $errorMessage : ""
        );
    }


}
