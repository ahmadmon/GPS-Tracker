<?php

namespace App\Providers;


use App\Enums\Subscription\SubscriptionStatus;
use App\Http\Services\Subscription\SubscriptionService;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('Subscription', SubscriptionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::loginUsingId(171);
    }
}
