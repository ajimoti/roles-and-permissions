<?php

namespace Tarzancodes\RolesAndPermissions\Concerns;

use Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException;

trait Authorizable
{
    /**
     * Check if the model has a role.
     *
     * @param string|array $role
     * @return bool
     */
    abstract public function hasRole(string|int $role): bool;

    /**
     * Check if the model has the permissions
     *
     * @param [type] ...$permissions
     * @return boolean
     */
    abstract public function has(...$permissions): bool;

    /**
     * Get the model's permissions
     *
     * @return array
     */
    abstract public function permissions(): array;

    /**
     * Assign the given role to the model.
     *
     * @param string $role
     * @return boolean
     */
    abstract public function assign(string $role): bool;

    /**
     * Remove role from model
     *
     * @return boolean
     */
    abstract public function removeRole(): bool;

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
