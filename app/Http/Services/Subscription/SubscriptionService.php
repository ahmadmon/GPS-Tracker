<?php

namespace App\Http\Services\Subscription;

use App\Models\Company;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * @param Wallet $wallet
     * @param SubscriptionPlan $plan
     * @param bool $AutoRenew
     * @return Subscription
     */
    public function
    subscribe(Wallet $wallet, SubscriptionPlan $plan, bool $AutoRenew = false): Subscription
    {
        return $wallet->subscription()->create([
            'subscription_plan_id' => $plan->id,
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addDays($plan->duration)->endOfDay(),
            'status' => 'active',
            'auto_renew' => $AutoRenew ? 1 : 0
        ]);
    }

    /**
     * @param Wallet $wallet
     * @param SubscriptionPlan $plan
     * @return void
     */
    public function
    subscribeSubsets(Wallet $wallet, SubscriptionPlan $plan): void
    {
        $company = $wallet->walletable;
        $company->load(['users', 'manager']);


        if ($company instanceof Company) {
            $company->users->push($company->manager)->map(function (User $user) use ($plan) {
                if (!$user->isSubscriber()) {
                    $this->subscribe($user->wallet, $plan);
                }
            });
        }
    }

    /**
     * @param Subscription $subscription
     * @return void
     */
    public function
    renew(Subscription $subscription): void
    {
        $subscription->load('plan:duration,id');

        $subscription->update([
            'end_at' => Carbon::create($subscription->start_at)->addDays($subscription->plan->duration)->endOfDay(),
            'status' => 'active'
        ]);
    }

    /**
     * @param Company $company
     * @return void
     */
    public function
    renewSubsets(Company $company): void
    {
        $company->load(['users', 'manager']);

        $company->users->push($company->manager)->map(function (User $user) {
            $userSubscription = $user->wallet->subscription;

            if ($userSubscription) {
                $this->renew($userSubscription);
            }
        });
    }

    /**
     * @param Subscription $subscription
     * @return bool
     */
    public function
    cancel(Subscription $subscription): bool
    {
        return $subscription->update([
            'status' => 'canceled',
            'canceled_at' => Carbon::now(),
        ]);
    }

    /**
     * @param Wallet $wallet
     * @return bool
     */
    public function
    activeSubscription(Wallet $wallet): bool
    {
        return $wallet->subscription()->where('status', 'active')->exists();
    }

}
