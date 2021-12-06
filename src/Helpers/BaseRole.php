<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use BenSampo\Enum\Enum;

class BaseRole extends Enum
{
    protected static $useHierarchy = false;

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

    final private static function usesHierarchy()
    {
        return static::$useHierarchy;
    }
}
