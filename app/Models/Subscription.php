<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Subscription extends Model
{
    protected $guarded = ['id'];


    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

//    public function subscriber(): HasOneThrough
//    {
//        return $this->hasOneThrough($this->wallet->walletable_type, Wallet::class);
//    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
