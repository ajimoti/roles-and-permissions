<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Tarzancodes\RolesAndPermissions\Exceptions\InvalidRoleHierarchyException;

class Holdable
{
    /**
     * Indicates that the permissions should be included in the result
     *
     * @var bool
     */
    protected $withPermissions = false;

    /**
     * Initialize class
     *
     * @return array
     */
    public function __construct(
        public string $roleClass,
        public string|int $role,
    ) {
        $this->checkRoleHierarchy();
    }

    /**
     * Indicates that the permissions should be included in the result
     *
     * @return self
     */
    public function withPermissions()
    {
        $this->withPermissions = true;

        return $this;
    }

    /**
     * Get the roles that are lower than the provided role
     *
     * When 'withPermissions' is set to true, we'd return a multidimensional array of the roles and permissions
     * the roles will be set as the key, and the permissions will be set as the value.
     *
     * Otherwise we just return an array of the roles.
     *
     * @return array
     */
    public function getLowerRoles(): array
    {
        // Only enum classes that uses hierarchy can have lower roles
        if (! $this->roleClass::usesHierarchy()) {
            return [];
        }

        $allRolesAndPermissions = $this->roleClass::permissions();
        $roles = collect($this->roleClass::getValues());

        $lowerRoles = $roles->splice(array_search($this->role, $roles->all()) + 1)->all();

        if ($this->withPermissions) {
            $rolesAndPermissions = [];

            foreach ($lowerRoles as $role) {
                $rolesAndPermissions[$role] = $allRolesAndPermissions[$role] ?? [];
            }

            return $rolesAndPermissions;
        }

        return $lowerRoles;
    }

    /**
     * Get the roles that are higher than the provided role
     *
     * When 'withPermissions' is set to true, we'd return a multidimensional array of the roles and permissions
     * the roles will be set as the key, and the permissions will be set as the value
     *
     * Otherwise we just return an array of the roles.
     *
     * @return array
     */
    public function getHigherRoles()
    {
        // Only enum classes that uses hierarchy can have higher roles
        if (! $this->roleClass::usesHierarchy()) {
            return [];
        }

        $allRolesAndPermissions = $this->roleClass::permissions();
        $roles = collect($this->roleClass::getValues());

        $higherRoles = $roles->splice(0, array_search($this->role, $roles->all()))->all();

        if ($this->withPermissions) {
            $rolesAndPermissions = [];

            foreach ($higherRoles as $role) {
                $rolesAndPermissions[$role] = $allRolesAndPermissions[$role] ?? [];
            }

            return $rolesAndPermissions;
        }

        return $higherRoles;
    }

    /**
     * Ensures the hierarchy of the roles declared in the enum class
     * is respected in the roles and permissions array of the `permissions()` method
     *
     * @return array
     */
    private function checkRoleHierarchy()
    {
        $roleConstants = $this->roleClass::getValues();
        $rolesInMethod = array_keys($this->roleClass::permissions());

        foreach ($rolesInMethod as $key => $role) {
            if ($role !== $roleConstants[$key]) {
                throw new InvalidRoleHierarchyException($role, $this->roleClass);
            }
        }
    }
}
