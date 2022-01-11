<?php

namespace Ajimoti\RolesAndPermissions\Helpers;

use Ajimoti\RolesAndPermissions\Collections\RoleCollection;
use Ajimoti\RolesAndPermissions\Exceptions\InvalidRoleHierarchyException;

class Holdable
{
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
     * Get the roles that are lower than the provided role
     *
     * @return RoleCollection
     */
    public function getLowerRoles(): RoleCollection
    {
        // Only enum classes that uses hierarchy can have lower roles
        if (! $this->roleClass::usesHierarchy()) {
            return [];
        }

        $roles = collect($this->roleClass::getValues());
        $lowerRoles = $roles->splice(array_search($this->role, $roles->all()) + 1)->all();

        return $this->roleClass::collect($lowerRoles);
    }

    /**
     * Get the roles that are higher than the provided role
     *
     * @return RoleCollection
     */
    public function getHigherRoles(): RoleCollection
    {
        // Only enum classes that uses hierarchy can have higher roles
        if (! $this->roleClass::usesHierarchy()) {
            return [];
        }

        $roles = collect($this->roleClass::getValues());
        $higherRoles = $roles->splice(0, array_search($this->role, $roles->all()))->all();

        return $this->roleClass::collect($higherRoles);
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
