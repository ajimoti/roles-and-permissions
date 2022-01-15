<?php

namespace Ajimoti\RolesAndPermissions\Traits;

use Illuminate\Support\Str;
use Ajimoti\RolesAndPermissions\Contracts\RolesContract;

trait SupportsMagicCalls
{
    /**
     * Method to call on the model to check if the model has the role.
     *
     * @return bool
     */
    abstract public function hasRole(): bool;

    /**
     * Method to call on the model to check if the model holds the permissions.
     *
     * @return bool
     */
    abstract public function holds(): bool;

    /**
     * Checks if the magic method called is to validate a role or a permission
     *
     * @param  string  $method
     * @return bool
     */
    private function isPossibleMagicCall($method): bool
    {
        // When the user uses the magic method is[Role]()
        // to check if the model has the role.
        // OR when the user uses the magic method can[Permission]()
        // to check if the model has the permission.
        return (Str::startsWith($method, 'is') && Str::length($method) > 2) ||
                (Str::startsWith($method, 'can') && Str::length($method) > 3);
    }

    public function performMagic($method, $enumClass)
    {
        if (Str::startsWith($method, 'is') && Str::length($method) > 2) {
            // When the user uses the magic method is[Role]()
            // to check if the model has the role.
            $role = substr($method, 2);
            return $this->hasRole(constant("{$enumClass}::{$role}"));

        } elseif (Str::startsWith($method, 'can') && Str::length($method) > 3) {
            // When the user uses the magic method can[Permission]()
            // to check if the model has the permission.
            $permission = substr($method, 3);
            $permissionClass = $enumClass::$permissionClass;

            return $this->holds(constant("{$permissionClass}::{$permission}"));
        }
    }
}
