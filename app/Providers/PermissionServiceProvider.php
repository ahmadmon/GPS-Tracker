<?php

namespace App\Providers;

use App\Http\Services\Permission\AclService;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('Acl', AclService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            Gate::before(
                fn(User $user): bool => $user->hasUserType('super-admin') ||
                    $user->hasUserType('admin') ||
                    $user->hasRole(['programmer']));


            Gate::define('has-permission', function ($user) {
                Log::info('Gate is running');
                return true;
            });


            Blade::if('role', fn($role): bool => auth()->check() && auth()->user()->hasRole($role));


        } catch (\Exception $e) {
            Log::error('PermissionServiceProvider Error: ' . $e->getMessage());
        }
    }
}
