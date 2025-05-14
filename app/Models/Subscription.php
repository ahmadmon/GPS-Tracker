<?php

namespace App\Models;

use App\Enums\Subscription\SubscriptionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Subscription extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'status' => SubscriptionStatus::class,
        'auto_renew' => 'boolean'
    ];


    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function cancellation(): HasOne
    {
        return $this->hasOne(SubscriptionCancellation::class);
    }
}
