<?php

namespace Tarzancodes\RolesAndPermissions;

use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Concerns\Authorizable;
use Tarzancodes\RolesAndPermissions\Helpers\PivotRelation;

trait HasRolesAndPermissions
{
    use Authorizable;

    protected Model $pivotModel;

    public function of(Model $model, string $relationshipName = null)
    {
        return new PivotRelation($this, $model, $relationshipName);
    }

    // can accept a permission, an array of permissions, or multiple parameters
    // public function can($permission)
    public function can($permission, $arguments = [])
    {
        $roleColumnName = config('roles-and-permissions.role_column_name');
        $roleEnum = config('roles-and-permissions.roles_enum.users');

        if ($role = $this->{$roleColumnName}) {
            return in_array($permission, $roleEnum::getPermissions($role));
        }

        return false;
    }

    public function has(...$permissions): bool
    {
        $roleColumnName = config('roles-and-permissions.role_column_name');
        $roleEnum = config('roles-and-permissions.roles_enum.users');

        $permissions = collect($permissions)->flatten()->all();

        if ($role = $this->{$roleColumnName}) {
            return $roleEnum::allPermissionsAreValid($role, $permissions);
        }

        return false;
    }

    public function permissions(): array
    {
        $roleColumnName = config('roles-and-permissions.role_column_name');
        $roleEnum = config('roles-and-permissions.roles_enum.users');

        return $roleEnum::getPermissions($this->{$roleColumnName});
    }

    public function assign(string|int $role): bool
    {
        $roleEnum = config('roles-and-permissions.roles_enum.users');
        if (! in_array($role, $roleEnum::getValues())) {
            throw new \InvalidArgumentException("The role `{$role}` does not exist.");
        }

        static::unguard();
        $updated = $this->update([config('roles-and-permissions.role_column_name') => $role]);
        static::reguard();

        return $updated;
    }

    public function removeRole(): bool
    {
        $roleColumnName = config('roles-and-permissions.role_column_name');

        static::unguard();
        $updated = $this->update([$roleColumnName => null]);
        static::reguard();

        return $updated;
    }
}
