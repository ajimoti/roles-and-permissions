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
     * Get the model's permissions
     *
     * @return array
     */
    abstract public function permissions(): array;

    /**
     * Assign the given role to the model.
     *
     * @param string|int|array $roles
     * @return bool
     */
    abstract public function assign(...$roles): bool;

    /**
     * Remove role from model
     *
     * @param string|int|array $roles
     * @return bool
     */
    abstract public function removeRole(...$roles): bool;

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
     * @param string|int $role
     * @return bool
     */
    public function authorizeRole(string|int $role): bool
    {
        if ($this->hasRole($role)) {
            return true;
        }

        throw new PermissionDeniedException('You are not authorized to perform this action.');
    }
}
