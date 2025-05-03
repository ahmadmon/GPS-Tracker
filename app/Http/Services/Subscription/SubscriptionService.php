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
     * @return Subscription
     */
    public function subscribe(Wallet $wallet, SubscriptionPlan $plan): Subscription
    {
        return $wallet->subscription()->create([
            'subscription_plan_id' => $plan->id,
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addDays($plan->duration)->endOfDay(),
            'status' => 'active',
        ]);
    }

    /**
     * @param Wallet $wallet
     * @return void
     */
    public function subscribeSubsets(Wallet $wallet): void
    {
        $company = $wallet->walletable;
        $company->load('users');

        $plan = $company->subscription->plan;

        if ($company instanceof Company)
            $company->users->map(fn(User $user) => $this->subscribe($wallet, $plan));
    }

    /**
     * @param Subscription $subscription
     * @return bool
     */
    public function renew(Subscription $subscription): bool
    {
        $subscription->load('plan:duration');

        return $subscription->update([
            'end_at' => Carbon::now()->addDays($subscription->plan->duration)->endOfDay(),
            'status' => 'active'
        ]);
    }

    /**
     * @param Subscription $subscription
     * @return bool
     */
    public function cancel(Subscription $subscription): bool
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
    public function activeSubscription(Wallet $wallet): bool
    {
        return $wallet->subscription()->where('status', 'active')->exists();
    }

}
