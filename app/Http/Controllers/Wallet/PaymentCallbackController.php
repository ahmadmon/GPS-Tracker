<?php

namespace App\Http\Controllers\Wallet;

use App\Facades\Acl;
use App\Http\Controllers\Controller;
use App\Http\Services\Payment\PaymentService;
use App\Jobs\SendSms;
use App\Models\Payment;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Shetabit\Multipay\Exceptions\InvoiceNotFoundException;
use Shetabit\Multipay\Exceptions\PreviouslyVerifiedException;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Exceptions\TimeoutException;

class PaymentCallbackController extends Controller
{
    /**
     * @param Request $request
     * @param WalletTransaction $transaction
     * @param Payment $payment
     * @param PaymentService $paymentService
     */
    public function __invoke(Request $request, WalletTransaction $transaction, Payment $payment, PaymentService $paymentService)
    {
        try {
            // Update the first response from the bank
            $payment->update(['bank_first_response' => json_encode($request->all())]);

            // Verify payment and get wallet
            $verifyResponse = $paymentService->paymentVerify((int)$transaction->amount, $payment);
            $wallet = $transaction->wallet;

            // Process payment
            if (strtoupper($request->Status) === 'OK') {
                $payment->update([
                    'status' => 'success',
                    'bank_second_response' => json_encode($verifyResponse, JSON_UNESCAPED_UNICODE)
                ]);

                $transaction->update(['status' => 'success']);
                $wallet->increment('balance', (int)$transaction->amount);

                // Sending a success message via SMS
                $isUser = $wallet->walletable instanceof User;
                $phoneNumber = $isUser ? $wallet->walletable->phone : $wallet->walletable->manager->phone;
                $message = $this->smsSuccessMessage($transaction->amount, $verifyResponse, $isUser, $wallet->walletable->name);
                SendSms::dispatch($phoneNumber, $message);

                return $this->getRedirectResponse('success', $wallet->id, $transaction->amount, $verifyResponse, $wallet->balance);

            } else {
                if ($verifyResponse) {
                    $payment->update([
                        'status' => 'failed',
                        'bank_second_response' => is_string($verifyResponse) ? $verifyResponse : json_encode($verifyResponse, JSON_UNESCAPED_UNICODE)
                    ]);

                    $transaction->update(['status' => 'failed']);

                    return $this->getRedirectResponse('failed', $wallet->id, $transaction->amount, $verifyResponse);
                }
            }
        } catch (\Exception $e) {
            Log::error('payment failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->getRedirectResponse('error', $transaction->wallet->id);
        }
    }


    /**
     * @param string $status
     * @param int $walletId
     * @param int|null $amount
     * @param $verifyResponse
     * @param int|null $balance
     * @return RedirectResponse
     */
    private function getRedirectResponse(string $status, int $walletId, int $amount = null, $verifyResponse = null, int $balance = null)
    {
        $role = Acl::getRole();
        $route = $this->getRouteBasedOnRole($role, $walletId);
        $message = $this->getMessageBasedOnStatusAndRole($status, $role, $amount, $verifyResponse, $balance);

        $status = $status === 'failed' ? 'error' : $status;
        return redirect($route)->with("{$status}-alert", $message);
    }

    /**
     * @param string $role
     * @param int|null $walletId
     * @return string
     */
    private function getRouteBasedOnRole(string $role, ?int $walletId): string
    {
        return in_array($role, ['admin', 'super-admin', 'developer'])
            ? route('wallet-management.show', $walletId)
            : route('profile.wallet');
    }

    /**
     * @param string $status
     * @param string $role
     * @param int|null $amount
     * @param $verifyResponse
     * @param int|null $balance
     * @return string
     */
    private function getMessageBasedOnStatusAndRole(string $status, string $role, int $amount = null, $verifyResponse = null, int $balance = null)
    {
        return match ($status) {
            'success' => $this->getSuccessMessageForRole($role, $amount, $verifyResponse, $balance),
            'failed' => $this->getFailedMessageForRole($role, $amount, $verifyResponse),
            'error' => $this->getErrorMessageForRole($role),
            default => 'مشکل غیرمنتظره‌ای به وجود آمده است.',
        };
    }


    /**
     * @param string $role
     * @param int $amount
     * @param array $verifyResponse
     * @param $balance
     * @return string
     */
    private function getSuccessMessageForRole(string $role, int $amount, array $verifyResponse, $balance): string
    {
        $commonMessage = sprintf(
            "💳 عملیات شارژ کیف پول با موفقیت تکمیل شد.\n\n" .
            "✳️ جزئیات تراکنش:\n" .
            "▫️ مبلغ: %s تومان\n" .
            "▫️ کد رهگیری: %s\n" .
            "▫️ زمان: %s\n\n" .
            "💰 موجودی فعلی: %s تومان\n\n",
            priceFormat($amount),
            $verifyResponse['referenceId'],
            jalaliDate($verifyResponse['date'], format: '%d %B %Y, H:i') ?? jalaliDate(now(), format: '%d %B %Y, H:i'),
            priceFormat($balance)
        );

        if (in_array($role, ['admin', 'super-admin', 'developer'])) {
            return $commonMessage;
        }

        return $commonMessage . "\nدر صورت هرگونه مشکل با پشتیبانی تماس بگیرید.";

    }

    /**
     * @param int $amount
     * @param array $verifyResponse
     * @param $isUser
     * @param $companyName
     * @return string
     *
     * Success Message for sms to sending user's phone
     */
    private function smsSuccessMessage(int $amount, array $verifyResponse, $isUser = false, $companyName = null): string
    {
        if (!$isUser && $companyName) {
            return sprintf(
                "سمفا - سامانه هوشمند رهیابی GPS\n" .
                "💳 شارژ کیف پول سازمان '%s' با موفقیت انجام شد.\n" .
                "💰 مبلغ: %s تومان\n" .
                "▫️ کد رهگیری: %s",
                $companyName,
                priceFormat($amount),
                $verifyResponse['referenceId']
            );
        }

        return sprintf(
            "سمفا - سامانه هوشمند رهیابی GPS\n" .
            "💳 شارژ کیف پول شما با موفقیت انجام شد.\n" .
            "💰 مبلغ: %s تومان\n" .
            "▫️ کد رهگیری: %s",
            priceFormat($amount),
            $verifyResponse['referenceId']
        );
    }

    /**
     * @param string $role
     * @param int $amount
     * @param array|string|null $verifyResponse
     * @return string
     */
    private function getFailedMessageForRole(string $role, int $amount, array|string|null $verifyResponse = null): string
    {
        $referenceId = is_array($verifyResponse) ? $verifyResponse['referenceId'] ?? '---' : '---';
        $date = is_array($verifyResponse) ? jalaliDate($verifyResponse['date'], format: '%d %B %Y, H:i') ?? jalaliDate(now(), format: '%d %B %Y, H:i') : jalaliDate(now(), format: '%d %B %Y, H:i');
        $errorMessage = is_string($verifyResponse) ? $verifyResponse : null;

        // Create a common transaction details message
        $transactionDetails = sprintf(
            "❌ عملیات پرداخت ناموفق بود.\n\n" .
            "✳️ جزئیات تراکنش:\n" .
            "▫️ مبلغ: %s تومان\n" .
            "▫️ کد رهگیری: %s\n" .
            "▫️ زمان: %s\n\n",
            priceFormat($amount),
            $referenceId,
            $date
        );

        // Create an error explanation message
        $errorExplanation = $errorMessage ? "🛑 توضیح خطا: " . $errorMessage : '';


        $roleMessage = in_array($role, ['admin', 'super-admin', 'developer'])
            ? "💡 در صورت کسر وجه، مبلغ طی ۷۲ ساعت آینده به کارت بانکی کاربر بازگردانده خواهد شد."
            : "💡 در صورت کسر وجه، مبلغ طی ۷۲ ساعت آینده به کارت بانکی شما بازگردانده خواهد شد.";

        // Return the full message
        return $transactionDetails . $errorExplanation . "\n" . $roleMessage;
    }

    /**
     * @param string $role
     * @return string
     */
    private function getErrorMessageForRole(string $role): string
    {
        $commonErrorMessage = "❌ مشکلی در پردازش پرداخت به وجود آمد.\n" .
            "در صورت کسر مبلغ، وجه تا ۷۲ ساعت آینده به حساب شما بازگردانده خواهد شد.\n" .
            "لطفاً در صورت نیاز با پشتیبانی تماس بگیرید.";

        if (in_array($role, ['admin', 'super-admin', 'developer'])) {
            return "❌ مشکلی در پردازش پرداخت به وجود آمد.\n" .
                "در صورت کسر مبلغ، وجه تا ۷۲ ساعت آینده به حساب کاربر بازگردانده خواهد شد.";
        }

        return $commonErrorMessage;
    }
}
