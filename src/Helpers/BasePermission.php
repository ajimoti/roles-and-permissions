<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Illuminate\Support\Collection;
use Tarzancodes\RolesAndPermissions\Collections\PermissionCollection;

abstract class BasePermission extends BaseEnum
{
    /**
     * The class used to wrap the values when the `collect()` method is called.
     *
     * @var Collection
     */
    protected static $collectionClass = PermissionCollection::class;

    /**
     * Set a description for the permissions
     *
     * @return string
     */
    public static function getDescription($value): string
    {
        return 'Can ' . strtolower(parent::getDescription($value));
    }
}
