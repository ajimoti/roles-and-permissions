<?php

namespace Tarzancodes\RolesAndPermissions\Concerns;

use Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException;

trait Authorizable
{
    /**
     * Check if the model has the permissions
     *
     * @param string|int|array $permissions
     * @return bool
     */
    abstract public function has(...$permissions): bool;

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    abstract public function hasRoles(...$roles): bool;

    /**
     * Check if the model has a permission.
     *
     * @param string|array $permission
     * @return bool
     */
    public function authorize(...$permissions): bool
    {
        if ($this->has(...$permissions)) {
            return true;
        }

        throw new PermissionDeniedException('You are not authorized to perform this action.');
    }

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function authorizeRole(...$role): bool
    {
        if ($this->hasRoles(...$role)) {
            return true;
        }

        throw new PermissionDeniedException('You are not authorized to perform this action.');
    }
}
