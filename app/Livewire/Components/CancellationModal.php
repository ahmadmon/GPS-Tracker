<?php

namespace App\Livewire\Components;

use App\Enums\Subscription\CancellationStatus;
use App\Enums\Subscription\SubscriptionStatus;
use App\Models\Subscription;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CancellationModal extends Component
{
    public Subscription $subscription;

    #[Validate('bool', as: 'نوع واریز')]
    public bool $walletRefund = false;
    #[Validate('required_if:walletRefund,false|nullable|string|regex:/^IR\d{24}$/', as: 'شماره شبا',
        message: [
            'required_if' => 'فیلد :attribute الزامی است.',
            'regex' => ":attribute معتبر نیست! فرمت صحیح: IR به همراه ۲۴ رقم"
        ])]
    public ?string $iban;
    #[Validate('required|string|min:5', as: 'دلیل لغو')]
    public string $reason;


    public function render()
    {
        $userSavedIbans = Cache::remember('user-ibans', 60 * 5, static function () {
            return DB::table('subscription_cancellations')
                ->join('subscriptions', 'subscriptions.id', '=', 'subscription_cancellations.subscription_id')
                ->where('subscriptions.wallet_id', auth()->user()->wallet->id)
                ->whereNotNull('subscription_cancellations.iban')
                ->distinct()
                ->pluck('subscription_cancellations.iban');
        });

        if ($userSavedIbans->count() === 1) {
            $this->iban = $userSavedIbans->first();
        }


        return view('livewire.components.cancellation-modal', [
            'userSavedIbans' => $userSavedIbans
        ]);
    }


    public function handleCancellation()
    {
        $inputs = (object)$this->validate();

        dd($inputs);

        $subscription = $this->subscription->load('plan', 'wallet', 'cancellation');
        if ($subscription->cancellation()->exists() && $subscription->cancellation->status->isPending()) {
            return to_route('profile.subscription.show')->with('error-alert', 'شما یک درخواست لغو در حال بررسی دارید. لطفاً تا مشخص شدن وضعیت آن صبر کنید.');
        }

        $startDate = Carbon::create($subscription->start_at);
        $now = Carbon::now();
        $diffInHours = $startDate->diffInHours($now);
        $diffInDays = $startDate->diffInDays($now);
        $plan = $subscription->plan;


        if ($diffInHours <= 24) {
            $refundAmount = $plan->price;
            $type = 'full-refund';
        } elseif ($diffInDays < ($plan->duration / 2)) {
            $refundAmount = $plan->price * 0.7;
            $type = '70%-refund';
        } else {
            return to_route('profile.subscription.show')
                ->with('error-alert', sprintf("متاسفانه، امکان لغو اشتراک و بازگشت وجه پس از گذشت %s روز از زمان فعالسازی وجود ندارد.\nمهلت مجاز برای لغو و دریافت بازگشت وجه، نصف مدت اشتراک شما می‌باشد.", $plan->duration / 2));
        }


        $subscription->cancellation()->create([
            'reason' => $inputs->reason,
            'iban' => !$inputs->walletRefund ? $inputs->iban : null,
            'refund_amount' => $refundAmount,
            'status' => $inputs->walletRefund ? CancellationStatus::REFUNDED : CancellationStatus::PENDING,
            'canceled_at' => now()
        ]);


        if ($refundAmount > 0 && $inputs->walletRefund) {

            DB::transaction(function () use ($subscription, $refundAmount, $type) {
                $subscription->update([
                    'status' => SubscriptionStatus::CANCELED,
                    'auto_renew' => false,
                    'canceled_at' => now()
                ]);

                $wallet = $subscription->wallet;
                $wallet->increment('balance', $refundAmount);

                $this->createTransaction([
                    'amount' => $refundAmount,
                    'description' => match ($type) {
                        '70%-refund' => 'بازگشت 70% مبلغ اشتراک به کیف پول به دلیل لغو اشتراک در کمتر از نصف مدت آن',
                        'full-refund' => 'بازگشت کل مبلغ اشتراک به کیف پول به دلیل لغو اشتراک در کمتر از 24 ساعت',
                        default => 'بازگشت مبلغ اشتراک به کیف پول'
                    }
                ], $wallet);
            });

            return to_route('profile.wallet')->with('success-alert', sprintf("اشتراک شما با موفقیت لغو شد.\nمبلغ %s تومان به کیف پول شما واریز شد.", priceFormat($refundAmount)));
        }

        return to_route('profile.subscription.show')->with('success-alert', "درخواست لغو اشتراک شما با موفقیت ثبت شد.\nهمکاران ما در اسرع وقت آن را بررسی خواهند کرد.");
    }


    /*
    |--------------------------------------------------------------------------
    | Private Helper Functions
    |--------------------------------------------------------------------------
    |
    |
    */

    private function createTransaction(array $info, Wallet $wallet): void
    {
        $wallet->transactions()->create([
            'source_id' => $wallet->walletable_id,
            'source_type' => $wallet->walletable_type,
            'type' => 'credit',
            'status' => 'success',
            'amount' => $info['amount'],
            'description' => $info['description'] ?? null,
        ]);
    }
}
