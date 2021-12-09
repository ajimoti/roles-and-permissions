<?php

namespace Tarzancodes\RolesAndPermissions\Concerns;

trait HasRoles
{
    /**
     * Get the roles.
     *
     * @return array
     */
    abstract public function getRoles(): array;

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
        foreach ($this->getRoles() as $role) {
            $allPermissions = array_merge($allPermissions, $roleEnumClass::getPermissions($role));
        }

        return array_unique($allPermissions);
    }
}
