<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

trait HasPermissions
{

    // Relations
    //--------------------------------------------
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    // caching Roles and Permissions
    //--------------------------------------------
    public function cachedRoles()
    {
        return Cache::remember("user_roles_{$this->id}", 60 * 60, fn() => $this->roles()->get());
    }

    public function cachedPermissions()
    {
        return Cache::remember("user_permissions_{$this->id}", 60 * 60, fn() => $this->permissions()->get());
    }

    // Clear cache on role or permission update
    //--------------------------------------------
    public function clearPermissionCache(): void
    {
        Cache::forget("user_roles_{$this->id}");
        Cache::forget("user_permissions_{$this->id}");
    }

    // Functions
    //--------------------------------------------

    public function hasPermission($permission): bool
    {
        return $this->cachedPermissions()->contains('title', $permission);
    }

    public function hasPermissionThroughRole($permission): bool
    {
        $permission = Permission::where('title', $permission)->with('roles')->first();
        if ($permission) {
            foreach ($permission->roles as $role) {
                if ($this->cachedRoles()->contains($role)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function hasPermissionTo(string $permission): bool
    {
        return $this->hasPermission($permission) || $this->hasPermissionThroughRole($permission);
    }

    public function hasRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->cachedRoles()->contains('title', $role))
                return true;
        }
        return false;
    }

}