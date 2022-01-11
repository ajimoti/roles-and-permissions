<?php

namespace Ajimoti\RolesAndPermissions\Exceptions;

use Exception;

class InvalidRoleHierarchyException extends Exception
{
    public function __construct($role, $enumClass)
    {
        parent::__construct("Invalid role hierarchy in the `permissions()` method of the `{$enumClass}` enum class. Role [`{$role}`] should be placed in the same hierarchy as it appears in your constants declaration");
    }
}
