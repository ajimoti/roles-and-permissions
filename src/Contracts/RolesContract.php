<?php

namespace Ajimoti\RolesAndPermissions\Contracts;

use Ajimoti\RolesAndPermissions\Collections\RoleCollection;

interface RolesContract extends PermissionsContract
{
    /**
     * Check if the model has a permission.
     *
     * @param string|int $permission
     * @return bool
     */
    public function can($permission, $arguments = []); // Do not set the return type, else it will flag an error when used in the User model.

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function hasRole(...$roles): bool;

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function hasRoles(...$roles): bool;

    /**
     * Get the model's roles.
     *
     * @return RoleCollection
     */
    public function roles(): RoleCollection;

    /**
     * Assign the given role to the model.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function assign(...$roles): bool;

    /**
     * Remove roles from model
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function removeRoles(): bool;

    /**
     * Remove roles from model
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function removeRole(): bool;

    /**
     * Check if the model has a role.
     *
     * @param string|int $role
     * @return bool
     */
    public function authorizeRole(...$role): bool;

    /**
     * Check if the model has a role.
     *
     * @param string|int $role
     * @return bool
     */
    public function authorizeRoles(...$role): bool;
}
