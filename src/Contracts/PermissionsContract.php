<?php

namespace Tarzancodes\RolesAndPermissions\Contracts;

interface PermissionsContract
{
    /**
     * Get the model's permissions
     *
     * @return array
     */
    public function permissions(): array;

    /**
     * Check if the model has the permissions
     *
     * @param string|int|array $permissions
     * @return bool
     */
    public function has(...$permissions): bool;

    /**
     * Check if the model has a permission.
     *
     * @param string|array $permission
     * @return bool
     */
    public function authorize(...$permissions): bool;
}
