<?php


namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static authorize(string $permission)
 * @method static hasRole(string $role)
 */
class Acl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Acl';
    }

}
