<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use BenSampo\Enum\Enum;

class BasePermission extends Enum
{
    /**
     * Get all permissions
     *
     * @return array
     */
    public static function all(): array
    {
        return static::getValues();
    }
}
