<?php

namespace Tarzancodes\RolesAndPermissions\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
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
        return in_array($permission, $this->permissions());
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

        return Check::all($permissions)->existsIn($this->permissions());
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

        $exitingRoles = $this->model->modelRoles()->whereIn($this->getRoleColumnName(), $roles)
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
        $roles = empty(func_get_args()) ? $this->getRoles() : func_get_args();

        return $this->model->modelRoles()->whereIn($this->getRoleColumnName(), $roles)->delete();
    }

    /**
     * Get the model's roles
     *
     * @return array
     */
    protected function getRoles(): array
    {
        return $this->model->modelRoles()->pluck($this->getRoleColumnName())->all();
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
