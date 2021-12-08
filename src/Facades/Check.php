<?php

namespace Tarzancodes\RolesAndPermissions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Tarzancodes\RolesAndPermissions\Helpers\Check
 */
class Check extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'check';
    }
}
