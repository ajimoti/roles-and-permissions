<?php

namespace Tarzancodes\RolesAndPermissions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tarzancodes\RolesAndPermissions\RolesAndPermissions
 */
class RolesAndPermissions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'roles-and-permissions';
    }
}
