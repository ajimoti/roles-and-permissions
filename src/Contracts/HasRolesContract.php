<?php

namespace Tarzancodes\RolesAndPermissions\Contracts;

interface HasRolesContract extends HasPermissionsContract
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
    public function hasRoles(...$roles): bool;

    /**
     * Get the model's roles.
     *
     * @return array
     */
    public function roles(): array;

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
     * Check if the model has a role.
     *
     * @param string|int $role
     * @return bool
     */
    public function authorizeRole(string|int $role): bool;
}
