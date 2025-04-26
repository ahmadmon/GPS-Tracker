<?php

namespace App\Models;

use App\Enums\Subscription\Plan\PlanType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class SubscriptionPlan extends Model
{
    protected $guarded = ['id'];


    protected $casts = [
        'type' => PlanType::class
    ];


    protected static function booted()
    {
        $cacheKey = 'subscription-plan';
        static::created(fn() => forgetCache($cacheKey));
        static::deleted(fn() => forgetCache($cacheKey));
        static::updated(fn() => forgetCache($cacheKey));
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
