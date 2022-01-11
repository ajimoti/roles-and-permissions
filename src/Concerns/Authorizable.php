<?php

namespace Ajimoti\RolesAndPermissions\Concerns;

use Ajimoti\RolesAndPermissions\Exceptions\PermissionDeniedException;

trait Authorizable
{
    /**
     * Check if the model has the permissions
     *
     * @param string|int|array $permissions
     * @return bool
     */
    abstract public function holds(...$permissions): bool;

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    abstract public function hasRole(...$roles): bool;

    /**
     * Check if the model has a permission.
     *
     * @param string|array $permission
     * @return bool
     */
    public function authorize(...$permissions): bool
    {
        if ($this->holds(...$permissions)) {
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
        if ($this->hasRole(...$role)) {
            return true;
        }

        throw new PermissionDeniedException('You are not authorized to perform this action.');
    }

    /**
     * Check if the model has roles.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function authorizeRoles(...$role): bool
    {
        return $this->authorizeRole(...$role);
    }
}
