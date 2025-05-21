<?php

namespace App\Models;

use App\Enums\Subscription\SubscriptionStatus;
use App\Facades\Subscription as SubscriptionFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Wallet extends Model
{
    protected $guarded = ['id'];

    /**
     * Get the parent walletable model (user or company).
     */
    public function walletable(): MorphTo
    {
        return $this->morphTo();
    }


    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }


    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', SubscriptionStatus::ACTIVE);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function hasSubscription(): bool
    {
        return SubscriptionFacade::activeSubscription($this);
    }
}
