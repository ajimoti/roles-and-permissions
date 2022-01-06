<?php

namespace Tarzancodes\RolesAndPermissions\Contracts;

use Tarzancodes\RolesAndPermissions\Collections\PermissionCollection;

interface PermissionsContract
{
    /**
     * Get the model's permissions
     *
     * @return PermissionCollection
     */
    public function permissions(): PermissionCollection;

    /**
     * Check if the model has the permissions
     *
     * @param string|int|array $permissions
     * @return bool
     */
    public function holds(...$permissions): bool;

    /**
     * Check if the model has a permission.
     *
     * @param string|array $permission
     * @return bool
     */
    public function authorize(...$permissions): bool;
}
