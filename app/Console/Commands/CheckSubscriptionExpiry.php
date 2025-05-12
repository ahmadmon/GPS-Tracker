<?php

namespace App\Console\Commands;

use App\Enums\Subscription\SubscriptionStatus;
use App\Jobs\SendSms;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Console\Command;

class CheckSubscriptionExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscriptions expiration and update status to expired if necessary';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptions = Subscription::with('wallet.walletable', 'plan:name,id')
            ->whereHas('plan', fn($q) => $q->where('is_lifetime', false))
            ->where('status', SubscriptionStatus::ACTIVE->value)
            ->whereDate('end_at', '<=', now())
            ->get();


        foreach ($subscriptions as $subscription) {
            $walletable = $subscription->wallet->walletable;
            $isUser = $walletable instanceof User;
            $user = $isUser ? $walletable : $walletable->manager;

            $subscription->status = SubscriptionStatus::EXPIRED->value;
            $subscription->save();

            SendSms::dispatch($user->phone, $this->smsExpiredSubscriptionMessage($subscription->plan, $user, $isUser, $walletable->name));

            $user->notify(new GenericNotification("⚠️ دسترسی شما به سامانه محدود شده است.\n اشتراک خود را تمدید کنید تا از امکانات بهره‌مند شوید.", "subscription-expired"));
        }
    }


    private function
    smsExpiredSubscriptionMessage($plan, $user, $isUser, $companyName): string
    {
        if (!$isUser && $companyName) {
            return sprintf(
                "سمفا - سامانه هوشمند ردیابی GPS\n\n" .
                "\nسلام %s عزیز، 👋" .
                "\nمی‌خواستیم به شما اطلاع بدیم که اشتراک %s سازمان '%s' اخیراً به پایان رسیده." .
                "امیدواریم از دسترسی خود به به سامانه لذت برده باشید. 😊 برای ادامه استفاده و بهره‌مندی از سامانه سمفا، می‌تونید به راحتی اشتراکتون رو از قسمت جزییات اشتراک سازمانتون تمدید کنید.🚀",
                $user->name,
                $plan->name,
                $companyName
            );
        }

        return sprintf(
            "سمفا - سامانه هوشمند ردیابی GPS\n\n" .
            "\nسلام %s عزیز، 👋" .
            "\nمی‌خواستیم به شما اطلاع بدیم که اشتراک %s شما اخیراً به پایان رسیده." .
            "امیدواریم از دسترسی خود به به سامانه لذت برده باشید. 😊 برای ادامه استفاده و بهره‌مندی از سامانه سمفا، می‌تونید به راحتی اشتراکتون رو از قسمت جزییات اشتراک تمدید کنید.🚀",
            $user->name,
            $plan->name
        );
    }
}
