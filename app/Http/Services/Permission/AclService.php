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

    public function authorize(string $permission)
    {
        if ($this->user->hasPermissionTo($permission)) {
            return true;
        } else {
            abort(403, 'دسترسی شما به این بخش از سامانه محدود شده است.');
        }
    }


    public function hasRole(string $role)
    {
        if ($this->user->hasRole([$role])) {
            return true;
        } else {
            abort(403, 'دسترسی شما به این بخش از سیستم محدود شده است.');
        }
    }
}
