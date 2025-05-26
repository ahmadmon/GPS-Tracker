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

    /**
     * @param string|array $permissions
     * @param $entity
     * @param bool $requireAll
     * @return true|void
     */
    public function authorize(string|array $permissions, $entity = null, bool $requireAll = false)
    {
        $result = $this->checkPermissions($permissions, $entity, $requireAll);

        if ($result) {
            return true;
        }

        abort(403, 'شما مجوز دسترسی به این بخش از سامانه را ندارید!');
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


    protected function checkPermissions(string|array $permissions, $entity = null, bool $requireAll = false): bool
    {
        $permissions = (array)$permissions;

        $results = collect($permissions)->map(function ($permission) use ($entity) {
            return $this->user->hasAccessTo($permission, $entity);
        });

        return $requireAll
            ? $results->every(fn($result) => $result)
            : $results->contains(true);
    }
}
