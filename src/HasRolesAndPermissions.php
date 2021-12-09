<?php

namespace Tarzancodes\RolesAndPermissions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tarzancodes\RolesAndPermissions\Concerns\Authorizable;
use Tarzancodes\RolesAndPermissions\Concerns\HasRoles;
use Tarzancodes\RolesAndPermissions\Facades\Check;
use Tarzancodes\RolesAndPermissions\Helpers\PivotHasRoleAndPermissions;
use Tarzancodes\RolesAndPermissions\Models\ModelRole;

trait HasRolesAndPermissions
{
    use Authorizable;
    use HasRoles;

    // protected $service;

    // public function __construct()
    // {
    //     $this->service = new ModelService($this);
    // }

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
        $roleEnumClass = $this->getRoleEnumClass();

        if ($role = $this->{$this->getRoleColumnName()}) {
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
        $roleEnumClass = $this->getRoleEnumClass();

        $permissions = collect($permissions)->flatten()->all();

        if ($role = $this->{$this->getRoleColumnName()}) {
            return Check::all($permissions)->existsIn($roleEnumClass::getPermissions($role));
        }

        return false;
    }

    /**
     * Checks if the model has all the given roles.
     *
     * @param string $role
     * @return bool
     */
    public function hasRoles(...$roles): bool
    {
        $roles = collect($roles)->flatten()->all();

        return Check::all($roles)->existsIn($this->getRoles());
    }

    /**
     * Get the model's roles.
     *
     * @return array
     */
    public function roles(): array
    {
        return $this->getRoles();
    }

    /**
     * Assign the given role to the model.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function assign(...$roles): bool
    {
        $roles = collect($roles)->flatten()->all();
        $roleEnumClass = $this->getRoleEnumClass();

        $exitingRoles = $this->modelRoles()->whereIn($this->getRoleColumnName(), $roles)
                            ->select(['id', 'model_id', 'model_type', $this->getRoleColumnName()])
                            ->get()->pluck($this->getRoleColumnName())
                            ->all();

        DB::beginTransaction();
        foreach ($roles as $role) {
            if (! in_array($role, $roleEnumClass::all())) {
                throw new InvalidArgumentException(
                    sprintf(
                            'The role "%s" does not exist on the "%s" enum class.',
                            $role,
                            $roleEnumClass
                        )
                );
            }

            if (in_array($role, $exitingRoles)) {
                // If the role already exists, we don't need to do anything.
                continue;
            }

            $bulkRolesToSave[] = new ModelRole([$this->getRoleColumnName() => $role]);
        }

        // Bulk insert the new roles
        $this->modelRoles()->saveMany($bulkRolesToSave ?? []);
        DB::commit();

        return true;
    }

    /**
     * Remove the model's role.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function removeRoles(): bool
    {
        $roles = empty(func_get_args()) ? $this->getRoles() : func_get_args();

        return $this->modelRoles()->whereIn($this->getRoleColumnName(), $roles)->delete();
    }

    /**
     * Get the name of the "role" column.
     *
     * @return string
     */
    private function getRoleColumnName(): string
    {
        return config('roles-and-permissions.pivot.column_name');
    }

    /**
     * Get the name of the "role" enum class.
     *
     * @return string
     */
    private function getRoleEnumClass(): string
    {
        return config('roles-and-permissions.roles_enum.users');
    }

    /**
     * Get the modelRoles relationship.
     */
    private function modelRoles()
    {
        return $this->morphMany(ModelRole::class, 'model');
    }

    /**
     * Get the model's roles
     *
     * @return array
     */
    protected function getRoles(): array
    {
        return $this->modelRoles()->pluck($this->getRoleColumnName())->all();
        // return $this->{$this->getRoleColumnName()};
    }
}
