<?php

namespace Tarzancodes\RolesAndPermissions\Concerns;

use Tarzancodes\RolesAndPermissions\Collections\PermissionCollection;
use Tarzancodes\RolesAndPermissions\Collections\RoleCollection;

trait HasRoles
{
    /**
     * Get the roles.
     *
     * @return RoleCollection
     */
    abstract public function getRoles(): RoleCollection;

    /**
     * Get the name of the "role" enum class.
     *
     * @return string
     */
    abstract public function getRoleEnumClass(): string;

    /**
     * Get the permissions.
     *
     * @return PermissionCollection
     */
    public function permissions(): PermissionCollection
    {
        return new PermissionCollection(
            $this->getRoles()->pluck('permissions')->flatten()->unique()->all()
        );
    }

    /**
     * Get the name of the "role" column.
     *
     * @return string
     */
    protected function getRoleColumnName(): string
    {
        return config('roles-and-permissions.column_name');
    }
}
