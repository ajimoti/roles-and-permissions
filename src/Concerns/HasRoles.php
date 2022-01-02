<?php

namespace Tarzancodes\RolesAndPermissions\Concerns;

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
     * @return array
     */
    public function permissions(): array
    {
        $roleEnumClass = $this->getRoleEnumClass();

        $allPermissions = [];
        foreach ($this->getRoles()->toArray() as $role) {
            $allPermissions = array_merge($allPermissions, $roleEnumClass::getPermissions($role));
        }

        return array_unique($allPermissions);
    }

    /**
     * Get the name of the "role" column.
     *
     * @return string
     */
    protected function getRoleColumnName(): string
    {
        return config('roles-and-permissions.pivot.column_name');
    }
}
