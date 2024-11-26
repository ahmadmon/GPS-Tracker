<?php

namespace App\Providers;

use App\Facades\Acl;
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
                fn(User $user): bool => $user->hasUserType('super-admin') || $user->hasRole(['programmer']));


//            Gate::define('has-permission', fn(User $user, string $permission): bool => $user->hasPermissionTo($permission));
            Gate::define('has-permission', fn(string $permission): bool => Acl::hasPermission($permission));


            Blade::if('role', fn($role): bool => auth()->check() && Acl::hasRole($role));

            Blade::if('notRole', fn($role): bool => auth()->check() && !Acl::hasRole($role));


        } catch (\Exception $e) {
            Log::error('PermissionServiceProvider Error: ' . $e->getMessage());
        }
    }
}
