<?php

namespace App\Http\Services\Permission;


use App\Models\User;

class AclService
{
    protected User $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function authorize(string $permission, $entity = null)
    {
        if ($this->user->hasAccessTo($permission, $entity)) {
            return true;
        } else {
            abort(403, 'دسترسی شما به این بخش از سامانه محدود شده است.');
        }
    }

    public function hasPermission(string $permission): bool
    {
        return $this->user->hasPermissionTo($permission);
    }


    public function hasRole(array $roles): bool
    {

        return $this->user->hasRole($roles);
    }

    public static function getRole()
    {
        return auth()->user()->roles()?->first()?->title;
    }
}
