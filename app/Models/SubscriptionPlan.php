<?php

namespace App\Models;

use App\Enums\Subscription\Plan\PlanType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $guarded = ['id'];


    protected $casts = [
        'type' => PlanType::class,
        'is_lifetime' => 'boolean'
    ];


    protected static function booted()
    {
        $cacheKey = ['subscription-plan', 'plan-list'];
        static::created(static fn() => forgetCache($cacheKey));
        static::deleted(static fn() => forgetCache($cacheKey));
        static::updated(static fn() => forgetCache($cacheKey));
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
