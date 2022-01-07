<?php

namespace Tarzancodes\RolesAndPermissions\Collections;

use Illuminate\Support\Collection;

class RoleCollection extends Collection
{
    /**
     * Initialize class
     *
     * @return array
     */
    public function __construct(
        private array $roles = [],
    ) {
        parent::__construct($roles);
    }

    /**
     * Get every permission available in a role collection
     *
     * @return PermissionCollection
     */
    public function getPermissions(): PermissionCollection
    {
        $permissions = new PermissionCollection();

        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        return $permissions->unique();
    }
}
