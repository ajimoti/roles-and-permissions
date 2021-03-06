<?php

namespace Ajimoti\RolesAndPermissions\Helpers;

use Ajimoti\RolesAndPermissions\Collections\PermissionCollection;
use Ajimoti\RolesAndPermissions\Collections\RoleCollection;
use Ajimoti\RolesAndPermissions\Tests\Enums\Permission;
use Illuminate\Support\Collection;

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
    public static $permissionClass = 'App\Enums\Permission';

    /**
     * The class used to wrap the values when the `collect()` method is called.
     *
     * @var Collection
     */
    public static $collectionClass = RoleCollection::class;

    /**
     * Indicates if the roles should be considered
     * in the hierarchy of how they appear in
     * the permissions() method.
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

    public function __construct($roleValue)
    {
        parent::__construct($roleValue);

        $this->permissions = static::getPermissions($roleValue);
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
                    $permissions = $permissionClass::getInstancesFromValues($rolesAndPermissions[$role] ?? []);

                    foreach (static::hold($role)->getLowerRoles()->toArray() as $lowerRole) {
                        $permissions = array_merge(
                            $permissions,
                            $permissionClass::getInstancesFromValues($rolesAndPermissions[$lowerRole] ?? [])
                        );
                    }

                    $allPermissions = array_merge($allPermissions, $permissions);
                } else {
                    $allPermissions = array_merge(
                        $allPermissions,
                        $permissionClass::getInstancesFromValues($rolesAndPermissions[$role])
                    );
                }
            }
        }

        return new PermissionCollection(array_values(array_unique($allPermissions)));
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
