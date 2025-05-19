<?php

namespace App\Console\Commands;

use App\Enums\Subscription\SubscriptionStatus;
use App\Facades\Subscription as SubscriptionFacade;
use App\Jobs\SendSms;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\GenericNotification;
use Illuminate\Console\Command;

class AutoRenewSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:auto-renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically renew expired subscriptions for users with enough balance.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $expiredSubscriptions = Subscription::with('wallet:balance,id,walletable_id,', 'wallet.walletable', 'plan:price,id,name')
            ->whereHas('plan', fn($q) => $q->where('is_lifetime', false))
            ->where('status', SubscriptionStatus::EXPIRED->value)
            ->whereDate('end_at', '<=', now())
            ->where('auto_renew', true)
            ->where('renewal_attempted', false)
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $wallet = $subscription->wallet;
            $walletable = $wallet->walletable;
            $isUser = $walletable instanceof User;
            $plan = $subscription->plan;
            $user = $isUser ? $walletable : $walletable->manager;

            if ($wallet->balance >= $plan->price) {
                SubscriptionFacade::renew($subscription);

                if (!$isUser) {
                    SubscriptionFacade::renewSubsets($walletable);
                }

                $wallet->decrement('balance', $plan->price);

                $this->createTransaction([
                    'amount' => $plan->price,
                    'description' => "برداشت برای تمدید اشتراک {$plan->name}"
                ], $wallet);

                $message = $this->smsSubscriptionSuccessMessage($plan,
                    $subscription->end_at,
                    $isUser,
                    $walletable->name ?? null
                );

                $user->notify(new GenericNotification("اشتراک '{$plan->name}' شما با موفقیت تمدید شد.", 'subscription_renewed'));
            } else {
                $subscription->update(['renewal_attempted' => true]);

                $message = $this->smsSubscriptionFailedMessage($plan,
                    $isUser,
                    $walletable->name ?? null
                );

                $user->notify(new GenericNotification("متاسفانه موجودی کیف پول شما برای تمدید اشتراک '{$plan->name}' کافی نیست.", 'subscription_renewed_failed'));
            }
            SendSms::dispatch($user->phone, $message);

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helper Function
    |--------------------------------------------------------------------------
    |
    |
    */


    private function createTransaction(array $info, Wallet $wallet)
    {
        $wallet->transactions()->create([
            'source_id' => $wallet->walletable_id,
            'source_type' => $wallet->walletable_type,
            'type' => 'debit',
            'status' => 'success',
            'amount' => $info['amount'],
            'description' => $info['description'] ?? null,
        ]);
    }

    private function
    smsSubscriptionSuccessMessage($plan, $expirationDate, $isUser, $companyName): string
    {
        if (!$isUser && $companyName) {
            return sprintf(
                "سمفا - سامانه هوشمند رهیابی GPS\n\n" .
                "🎉 اشتراک '%s' برای سازمان '%s' با موفقیت تمدید شد.\n" .
                "📅 تاریخ انقضا: %s\n\n" .
                "برای مشاهده اشتراک، به جزئیات اشتراک مراجعه کنید.",
                $plan->name,
                $companyName,
                jalaliDate($expirationDate)
            );
        }

        return sprintf(
            "سمفا - سامانه هوشمند رهیابی GPS\n\n" .
            "🎉 اشتراک '%s' برای شما با موفقیت تمدید شد.\n" .
            "📅 تاریخ انقضا: %s\n\n" .
            "برای مشاهده اشتراک، به جزئیات اشتراک مراجعه کنید.",
            $plan->name,
            jalaliDate($expirationDate)
        );
    }

    private function
    smsSubscriptionFailedMessage($plan, $isUser, $companyName): string
    {
        if (!$isUser && $companyName) {
            return sprintf(
                "سمفا - سامانه هوشمند رهیابی GPS\n\n" .
                "⭕ متاسفانه موجودی کیف پول شما برای تمدید اشتراک '%s' سازمان '%s' کافی نیست.\n" .
                $plan->name,
                $companyName,
            );
        }

        return "سمفا - سامانه هوشمند رهیابی GPS\n\n" .
            "⭕ متاسفانه موجودی کیف پول شما برای تمدید اشتراک '%s' کافی نیست.\n" .
            $plan->name;
    }
}
