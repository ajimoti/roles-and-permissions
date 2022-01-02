<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Illuminate\Support\Collection;
use Tarzancodes\RolesAndPermissions\Tests\Enums\Permission;
use Tarzancodes\RolesAndPermissions\Collections\RoleCollection;
use Tarzancodes\RolesAndPermissions\Collections\PermissionCollection;

abstract class BaseRole extends BaseEnum
{
    /**
     * The permissions of the role passed in the constructor.
     *
     * @var PermissionCollection
     */
    public PermissionCollection $permissions;

    /**
     * The permission enum class used by this role.
     *
     * @var BasePermission
     */
    protected static $permissionClass = Permission::class;

    /**
     * The class used to wrap the values when the `collect()` method is called.
     *
     * @var Collection
     */
    protected static $collectionClass = RoleCollection::class;

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
     * This property is only used for pivot roles.
     *
     * Indicates if the model should be deleted
     * when the role is removed from the model.
     *
     * @return bool
     */
    protected static $deletePivotOnRemove = false;

    public function __construct($enumValue)
    {
        parent::__construct($enumValue);

        $this->permissions = static::getPermissions($enumValue);
    }

    /**
     * Get specific role permissions
     *
     * @param string|int $role
     * @return PermissionCollection
     */
    final public static function getPermissions(...$roles): PermissionCollection
    {
        $rolesAndPermissions = static::permissions();
        $roles = collect($roles)->flatten()->all();

        $allPermissions = [];
        foreach ($roles as $role) {
            if (! in_array($role, static::getValues())) {
                throw new \Exception("Invalid role `{$role}` supplied");
            }

            $permissionClass = static::$permissionClass;
            if (isset($rolesAndPermissions[$role])) {
                if (static::usesHierarchy()) {
                    // Return the present role's permissions,
                    // and permissions of roles that are lower than this in the array. (i.e roles with lower indexes)
                    $permissions = $permissionClass::getInstanceFromValues($rolesAndPermissions[$role] ?? []);

                    foreach (static::hold($role)->getLowerRoles() as $lowerRole) {
                        $permissions = array_merge(
                            $permissions, $permissionClass::getInstanceFromValues($rolesAndPermissions[$lowerRole] ?? [])
                        );
                    }

                    $allPermissions = array_merge($allPermissions, $permissions);
                } else {
                    $allPermissions = array_merge(
                        $allPermissions, $permissionClass::getInstanceFromValues($rolesAndPermissions[$role])
                    );
                }
            }
        }

        return new PermissionCollection(array_values(array_unique($allPermissions)));
    }

    /**
     * Get all roles
     *
     * @return RoleCollection
     */
    final public static function all(): RoleCollection
    {
        return new RoleCollection(
            static::getInstanceFromValues(static::getValues())
        );
    }

    /**
     * Set a description for the roles
     *
     * @return string
     */
    public static function getDescription($value): string
    {
        return parent::getDescription($value);
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
    final public static function usesHierarchy(): bool
    {
        return static::$useHierarchy;
    }

    /**
     * Get the roles in the hierarchy they appear in.
     *
     * @return array
     */
    final public static function rolesInHierarchy(): array
    {
        return static::getValues();
    }

    /**
     * Attempt to instantiate an enum by calling the enum key as a static method.
     *
     * This function defers to the macroable __callStatic function if a macro is found using the static method called.
     *
     * @param  string  $method
     * @param  mixed  $parameters
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if ($name === 'hold') {
            return new Holdable(static::class, ...$arguments);
        }

        return parent::__callStatic($name, $arguments);
    }
}
