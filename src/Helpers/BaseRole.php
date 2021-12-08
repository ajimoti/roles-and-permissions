<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use BenSampo\Enum\Enum;

abstract class BaseRole extends Enum
{
    /**
     * Indicates if the roles should be considered
     * in the hierarchy of how they appear in
     * the permissions() method.
     *
     *
     * Meaning if this is set to true,
     * a role will have the default permissions assigned to it,
     * and the permissions of the roles below them.
     *
     * @return bool
     */
    protected static $useHierarchy = false;

    /**
     * Indicates if the model should be deleted
     * when the role is removed from the model.
     *
     * @return bool
     */
    protected static $deletePivotOnRemove = false;

    /**
     * Get specific role permissions
     *
     * @param string|int $role
     * @return array
     */
    final public static function getPermissions($role): array
    {
        $rolesAndPermissions = static::permissions();

        if (! array_key_exists($role, $rolesAndPermissions)) {
            throw new \Exception("Invalid role `{$role}` supplied");
        }

        // Return the present role's permissions,
        // and permissions of roles with index higher than this
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

    /**
     * Get all roles
     *
     * @return array
     */
    final public static function all(): array
    {
        return static::getValues();
    }

    /**
     * Check if the model should be deleted when the role is removed.
     *
     * @return bool
     */
    final public static function deletePivotOnRemove(): bool
    {
        return static::$deletePivotOnRemove;
    }

    /**
     * Check if the role uses the hierarchy
     *
     * @return bool
     */
    final private static function usesHierarchy(): bool
    {
        return static::$useHierarchy;
    }
}
