<?php

namespace App\Console\Commands;


use App\Jobs\SendSms;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionExpiryNotification;
use Illuminate\Console\Command;

class SendSubscriptionExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:expiry-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS notifications for subscriptions nearing expiry';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptions = Subscription::with(['wallet', 'wallet.walletable'])
            ->where('status', 'active')
            ->where('end_at', '<=', now()->addDays(2))
            ->get();



        foreach ($subscriptions as $subscription) {

            $wallet = $subscription->wallet;
            $walletable = $wallet->walletable;

            $isUser = $walletable instanceof User;
            $user = $isUser ? $wallet->walletable : $walletable->manager;

            $message = $this->message($isUser, $walletable->name, $isUser ? $walletable->name : $walletable->manager->name);
            SendSms::dispatch($user->phone, $message);

            $this->info('Expire subscription SMS notification Command Sent.');

            $user->notify(new SubscriptionExpiryNotification("اشتراک شما به زودی منقضی خواهد شد.\n لطفا آن را تمدید کنید."));

        }
    }


    private function message(bool $isUser, string $companyName, string $name): string
    {
        if ($isUser) {
            return sprintf(
                "سمفا - سامانه هوشمند ردیابی GPS\n\n" .
                "%s عزیز اشتراک شما به زودی منقضی خواهد شد.\n" .
                "لطفا آن را تمدید کنید.",
                $name
            );
        }

        return sprintf(
            "سمفا - سامانه هوشمند ردیابی GPS\n\n" .
            "%s عزیز اشتراک سازمان '%s' به زودی منقضی خواهد شد.\n" .
            "لطفا آن را تمدید کنید.",
            $name,
            $companyName
        );
    }
}
