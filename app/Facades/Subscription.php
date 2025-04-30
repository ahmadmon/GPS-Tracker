<?php


namespace App\Facades;

use App\Models\Subscription as SubscriptionModel;
use App\Models\SubscriptionPlan;
use App\Models\Wallet;
use Illuminate\Support\Facades\Facade;

/**
 * @method subscribe(Wallet $wallet, SubscriptionPlan $plan)
 * @method subscribeSubsets(Wallet $wallet, SubscriptionPlan $plan)
 * @method renew(SubscriptionModel $subscription)
 * @method cancel(SubscriptionModel $subscription)
 * @method activeSubscription(Wallet $wallet)
 */
class Subscription extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Subscription';
    }

}
