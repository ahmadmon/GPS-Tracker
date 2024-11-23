<?php


namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static authorize(string $permission, $entity = null)
 * @method static hasRole(array $roles)
 * @method static hasPermission(string $permission)
 * @method static getRole()
 */
class Acl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Acl';
    }

}
