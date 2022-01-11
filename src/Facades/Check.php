<?php

namespace Ajimoti\RolesAndPermissions\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ajimoti\RolesAndPermissions\Helpers\Check
 */
class Check extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'check';
    }
}
