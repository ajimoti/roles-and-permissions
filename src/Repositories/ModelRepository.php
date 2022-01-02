<?php

namespace Tarzancodes\RolesAndPermissions\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Tarzancodes\RolesAndPermissions\Collections\RoleCollection;
use Tarzancodes\RolesAndPermissions\Concerns\Authorizable;
use Tarzancodes\RolesAndPermissions\Concerns\HasRoles;
use Tarzancodes\RolesAndPermissions\Contracts\RolesContract;
use Tarzancodes\RolesAndPermissions\Facades\Check;
use Tarzancodes\RolesAndPermissions\Models\ModelRole;

class ModelRepository implements RolesContract
{
    use Authorizable;
    use HasRoles;

    public function __construct(
        protected Model $model
    ) {
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
        return in_array($permission, $this->permissions()->toArray());
    }

    /**
     * Checks if the model has all the given permissions.
     *
     * @param string $permissions
     * @return bool
     */
    public function has(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten()->all();

        return Check::all($permissions)->existsIn($this->permissions()->toArray());
    }

    /**
     * Checks if the model has all the given roles.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(...$roles): bool
    {
        $roles = collect($roles)->flatten()->all();

        return Check::all($roles)->existsIn($this->getRoles()->toArray());
    }

    /**
     * Get the model's roles.
     *
     * @return RoleCollection
     */
    public function roles(): RoleCollection
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

        $exitingRoles = $this->model->modelRoles()->whereIn($this->getRoleColumnName(), $roles)
                            ->select($this->getRoleColumnName())
                            ->get()->pluck($this->getRoleColumnName())
                            ->all();

        DB::beginTransaction();
        foreach ($roles as $role) {
            if (! in_array($role, $roleEnumClass::all()->toArray())) {
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
        $this->model->modelRoles()->saveMany($bulkRolesToSave ?? []);
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
        $roles = empty(func_get_args()) ? $this->getRoles()->toArray() : func_get_args();

        return $this->model->modelRoles()->whereIn($this->getRoleColumnName(), $roles)->delete();
    }

    /**
     * Get the model's roles
     *
     * @return RoleCollection
     */
    protected function getRoles(): RoleCollection
    {
        $roleClass = $this->getRoleEnumClass();
        $roles = $this->model->modelRoles()->pluck($this->getRoleColumnName())->all();

        foreach ($roles as $role) {
            // Cast the roles to the correct type
            // This is needed because the roles are stored as strings in the database
            if (is_numeric($role)) {
                $role = (int) $role;
            }

            $cleanRoles[] = new $roleClass($role);
        }

        return new RoleCollection($cleanRoles ?? []);
    }

    /**
     * Get the name of the "role" enum class.
     *
     * @return string
     */
    private function getRoleEnumClass(): string
    {
        $tableName = $this->model->getTable();

        return config("roles-and-permissions.roles_enum.{$tableName}") ??
                config('roles-and-permissions.roles_enum.default');
    }
}
