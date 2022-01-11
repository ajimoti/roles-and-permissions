<?php

namespace Ajimoti\RolesAndPermissions\Helpers;

use Ajimoti\RolesAndPermissions\Collections\PermissionCollection;
use Illuminate\Support\Collection;

abstract class BasePermission extends BaseEnum
{
    /**
     * The class used to wrap the values when the `collect()` method is called.
     *
     * @var Collection
     */
    public static $collectionClass = PermissionCollection::class;

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
