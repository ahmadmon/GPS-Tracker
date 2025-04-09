<?php

namespace App\Http\Services\Payment;

use App\Models\WalletTransaction;
use Exception;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Exceptions\InvoiceNotFoundException;
use Shetabit\Multipay\Exceptions\PreviouslyVerifiedException;
use Shetabit\Multipay\Exceptions\PurchaseFailedException;
use Shetabit\Multipay\Exceptions\TimeoutException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use App\Models\Payment as PaymentModel;

class PaymentService
{
    /**
     * @param WalletTransaction $transaction
     * @param PaymentModel $payment
     * @return mixed
     * @throws Exception
     */
    public function paymentPage(WalletTransaction $transaction,PaymentModel $payment): mixed
    {
        $invoice = new Invoice;
        $invoice->amount((int)$transaction->amount);
        $invoice->detail(['title' => "سمفا - سامانه هوشمند ردیابی GPS", 'description' => "شارژ کیف پول کاربر جهت استفاده در سامانه سمفا"]);

        $callBackRoute = route('profile.callback-payment', [$transaction, $payment]);

        $paymentProcessor = Payment::callbackUrl($callBackRoute)
            ->purchase($invoice, function ($driver, $transactionId) use ($payment) {
            $payment->update([
                "gateway" => 'زرین پال',
                "transaction_id" => $transactionId,
            ]);
        });

        return $paymentProcessor->pay()->getAction();
    }

    /**
     * @param $amount
     * @param PaymentModel $payment
     * @return array|string
     * @throws InvoiceNotFoundException
     * @throws PreviouslyVerifiedException
     * @throws PurchaseFailedException
     * @throws TimeoutException
     *
     */
    public function paymentVerify($amount, PaymentModel $payment): array|string
    {
        try {
            $receipt = Payment::amount($amount)->transactionId($payment->transaction_id)->verify();

            return [
                'referenceId' => $receipt->getReferenceId(),
                'driver' => $receipt->getDriver(),
                'date' => $receipt->getDate()->toDayDateTimeString(),
            ];
        } catch (InvalidPaymentException $exception) {

            return $exception->getMessage();
        }
    }
}
