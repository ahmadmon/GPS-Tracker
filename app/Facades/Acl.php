<?php


namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method authorize(string $permission, $entity = null)
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
