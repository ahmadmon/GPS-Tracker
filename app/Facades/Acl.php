<?php


namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method authorize(string|array $permissions, $entity = null, bool $requireAll = false)
 * @method hasRole(array $roles)
 * @method hasPermission(string $permission)
 * @method getRole()
 */
class Acl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Acl';
    }

}
