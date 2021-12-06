<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use BenSampo\Enum\Enum;

class BaseRole extends Enum
{
    // Indicates if the roles should be considered in the hierarchy of how they are in the permissions() method.
    // This means the roles will have permissions assigned to them, and the permissions of the roles below them.
    protected static $useHierarchy = false;

    // Indicates if the user or pivot role should be deleted when this role is removed from the user.
    protected static $deletePivotOnRemove = false;

    final public static function getPermissions($role): array
    {
        $rolesAndPermissions = static::permissions();

        if (! array_key_exists($role, $rolesAndPermissions)) {
            throw new \Exception("Invalid role `{$role}` supplied");
        }

        // Return the present role's permissions, and permissions of roles with index higher than this
        if (self::usesHierarchy()) {
            $rolesInHierarchy = array_keys($rolesAndPermissions);
            $lowerRoles = collect($rolesInHierarchy)->splice(array_search($role, $rolesInHierarchy))->all();

            $permissions = [];
            foreach ($lowerRoles as $lowerRole) {
                $permissions = array_merge($permissions, $rolesAndPermissions[$lowerRole]);
            }

            return array_unique($permissions);
        }

        return $rolesAndPermissions[$role];
    }

    final public static function allPermissionsAreValid(string|int $role, array $permissions): bool
    {
        // Verify every value in the array is a valid permission
        return ! array_diff($permissions, static::getPermissions($role));
    }

    final public static function all(): array
    {
        return static::getValues();
    }

    final public static function deletePivotOnRemove(): bool
    {
        return static::$deletePivotOnRemove;
    }

    final private static function usesHierarchy(): bool
    {
        return static::$useHierarchy;
    }
}
