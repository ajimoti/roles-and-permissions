<?php

namespace Tarzancodes\RolesAndPermissions;

use Illuminate\Database\Eloquent\Model;

trait HasRolesAndPermissions
{
    protected $pivotModel;

    public function of(Model $model, string $relationshipName = null)
    {
        $this->pivotModel = $model;

        return $this;
    }

    // can accept a permission, an array of permissions, or multiple parameters
    public function can($permission)
    {
        $roleKey = config('roles-and-permissions.role_key');
        $roleClass = config('roles-and-permissions.roles_class');
        $rolesAndPermissions = $roleClass::rolesAndPermissions();

        if (array_key_exists($roleKey, $rolesAndPermissions)) {
            $validPermissions = $rolesAndPermissions[$roleKey];

            if (is_array($permission)) {
                // Ensure every value in the array is a valid permission
                return ! array_diff($permission, $validPermissions);
            }

            return in_array($permission, $validPermissions);
        }

        return false;
    }

    // can accept a permission, an array of permissions, or multiple parameters
    public function hasRole($role): bool
    {
        $roleKey = config('roles-and-permissions.role_key');

        if ($this->where($roleKey, $role)->exists()) {
            return true;
        }

        return false;
    }
}
