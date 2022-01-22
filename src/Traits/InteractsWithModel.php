<?php

namespace Ajimoti\RolesAndPermissions\Traits;

use Ajimoti\RolesAndPermissions\Collections\RoleCollection;
use Ajimoti\RolesAndPermissions\Collections\PermissionCollection;

trait InteractsWithModel
{
    /*
    |--------------------------------------------------------------------------
    | Information
    |--------------------------------------------------------------------------
    |
    | This trait basically acts as an interface for other traits that interact
    | with a model.
    |
    | Since Traits are not allowed to implement interfaces in PHP, we have to
    | use this trait to act as an interface.
    */

    /**
     * Checks if the model has the given permission.
     *
     * @param string $permission
     * @param array $arguments
     * @return bool
     */
    abstract public function can($permission, $arguments = []);

    /**
     * Checks if the model has all the given permissions.
     *
     * @param string|int|array|PermissionCollection $permissions
     * @return bool
     */
    abstract public function holds(...$permissions): bool;

    /**
     * Checks if the model has all the given roles.
     *
     * @param string|int|array|RoleCollection $role
     * @return bool
     */
    abstract public function hasRole(...$roles): bool;

    /**
     * Checks if the model has all the given roles.
     *
     * @param string|int|array|RoleCollection $role
     * @return bool
     */
    abstract public function hasRoles(...$roles): bool;

    /**
     * Get the model's roles.
     *
     * @return RoleCollection
     */
    abstract public function roles(): RoleCollection;

    /**
     * Get all the model's permissions.
     *
     * @return PermissionCollection
     */
    abstract public function permissions(): PermissionCollection;

    /**
     * Assign the given role to the model.
     *
     * @param string|int|array $roles
     * @return bool
     */
    abstract public function assign(): bool;

    /**
     * Remove the model's role.
     *
     * @param string|int|array $roles
     * @return bool
     */
    abstract public function removeRoles(): bool;

    /**
     * Remove the model's role.
     *
     * @param string|int|array $roles
     * @return bool
     */
    abstract public function removeRole(...$role): bool;

    /**
     * Check if the model has a permission.
     *
     * @param string|int|array $permission
     * @return bool
     */
    abstract public function authorize(...$permissions): bool;

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    abstract public function authorizeRoles(...$role): bool;

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    abstract public function authorizeRole(...$role): bool;
}
