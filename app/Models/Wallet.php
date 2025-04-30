<?php

namespace App\Models;

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
        return $this->hasOne(Subscription::class);
    }

    public function hasSubscription()
    {
        return SubscriptionFacade::activeSubscription($this);
    }
}
