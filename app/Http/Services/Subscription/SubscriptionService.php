<?php

namespace App\Http\Services\Subscription;

use App\Models\Company;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * @param User|Company $subscribable
     * @param SubscriptionPlan $plan
     * @return Company|User
     */
    public function subscribe(User|Company $subscribable, SubscriptionPlan $plan): Company|User
    {
        return $subscribable->create([
            'subscribe_plan_id' => $plan->id,
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addDays($plan->duration),
            'status' => 'active',
        ]);
    }

    /**
     * @param Company $company
     * @return void
     */
    public function subscribeSubsets(Company $company): void
    {
        $company->load('users');
        $plan = $company->subscription->plan;

        $company->users->map(fn(User $user) => $this->subscribe($user, $plan));
    }

    /**
     * @param Subscription $subscription
     * @return bool
     */
    public function renew(Subscription $subscription): bool
    {
        $subscription->load('plan:duration');

        return $subscription->update([
            'end_at' => Carbon::now()->addDays($subscription->plan->duration),
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

    public function activeSubscription(User|Company $subscribable)
    {
        return $subscribable->subscription()->where('status', 'active')->first();
    }

}
