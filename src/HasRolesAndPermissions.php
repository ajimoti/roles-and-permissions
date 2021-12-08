<?php

namespace Tarzancodes\RolesAndPermissions;

use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Concerns\Authorizable;
use Tarzancodes\RolesAndPermissions\Facades\Check;
use Tarzancodes\RolesAndPermissions\Helpers\PivotHasRoleAndPermissions;

trait HasRolesAndPermissions
{
    use Authorizable;

    /**
     * A model may have multiple roles.
     *
     * @return Tarzancodes\RolesAndPermissions\Helpers\PivotHasRoleAndPermissions
     */
    public function of(Model $model, string $relationshipName = null): PivotHasRoleAndPermissions
    {
        return new PivotHasRoleAndPermissions($this, $model, $relationshipName);
    }

    /**
     * Checks if the model has the given permission.
     *
     * @param string $permission
     * @param array $arguments
     * @return bool
     */
    public function can($permission, $arguments = [])
    {
        $roleEnumClass = $this->roleEnumClass();

        if ($role = $this->{$this->roleColumnName()}) {
            return in_array($permission, $roleEnumClass::getPermissions($role));
        }

        return false;
    }

    /**
     * Checks if the model has all the given permissions.
     *
     * @param string $permissions
     * @return bool
     */
    public function has(...$permissions): bool
    {
        $roleEnumClass = $this->roleEnumClass();

        $permissions = collect($permissions)->flatten()->all();

        if ($role = $this->{$this->roleColumnName()}) {
            return Check::forAll($permissions)->in($roleEnumClass::getPermissions($role));
        }

        return false;
    }

    /**
     * Checks if the model has all the given roles.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string|int $role): bool
    {
        return $this->{$this->roleColumnName()} === $role;
    }

    /**
     * Get all the model's permissions.
     *
     * @return array
     */
    public function permissions(): array
    {
        $roleEnumClass = $this->roleEnumClass();

        return $roleEnumClass::getPermissions($this->{$this->roleColumnName()});
    }

    /**
     * Assign the given role to the model.
     *
     * @param int|string $role
     * @return bool
     */
    public function assign(string|int $role): bool
    {
        $roleEnumClass = $this->roleEnumClass();
        if (! in_array($role, $roleEnumClass::getValues())) {
            throw new \InvalidArgumentException("The role `{$role}` does not exist.");
        }

        static::unguard();
        $updated = $this->update([$this->roleColumnName() => $role]);
        static::reguard();

        return $updated;
    }

    /**
     * Revoke the model's role.
     *
     * @return bool
     */
    public function removeRole(): bool
    {
        static::unguard();
        $updated = $this->update([$this->roleColumnName() => null]);
        static::reguard();

        return $updated;
    }

    /**
     * Get the name of the "role" column.
     *
     * @return string
     */
    private function roleColumnName(): string
    {
        return config('roles-and-permissions.pivot.role_column_name');
    }

    /**
     * Get the name of the "role" enum class.
     *
     * @return string
     */
    private function roleEnumClass(): string
    {
        return config('roles-and-permissions.roles_enum.users');
    }
}
