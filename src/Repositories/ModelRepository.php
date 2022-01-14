<?php

namespace Ajimoti\RolesAndPermissions\Repositories;

use Ajimoti\RolesAndPermissions\Collections\PermissionCollection;
use Ajimoti\RolesAndPermissions\Collections\RoleCollection;
use Ajimoti\RolesAndPermissions\Concerns\Authorizable;
use Ajimoti\RolesAndPermissions\Concerns\HasRoles;
use Ajimoti\RolesAndPermissions\Contracts\DirectPermissionsContract;
use Ajimoti\RolesAndPermissions\Contracts\RolesContract;
use Ajimoti\RolesAndPermissions\Facades\Check;
use Ajimoti\RolesAndPermissions\Models\ModelPermission;
use Ajimoti\RolesAndPermissions\Models\ModelRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ModelRepository implements RolesContract, DirectPermissionsContract
{
    use Authorizable;
    use HasRoles {
        permissions as rolesPermissions;
    }

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
    public function holds(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten()->all();

        return Check::all($permissions)->existsIn(
            $this->permissions()->merge($this->directPermissions())->toArray()
        );
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
     * Checks if the model has all the given roles.
     *
     * @param string $role
     * @return bool
     */
    public function hasRoles(...$roles): bool
    {
        return $this->hasRole(collect($roles)->flatten()->all());
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
     * Remove the model's role.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function removeRole(...$roles): bool
    {
        return $this->removeRoles(...$roles);
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
     * Give the model's permissions directly.
     *
     * @return bool
     */
    public function give(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten()->all();
        $roleEnumClass = $this->getRoleEnumClass();

        $exitingPermissions = $this->model->modelPermissions()->whereIn('permission', $permissions)
                            ->get()->pluck('permission')
                            ->all();

        DB::beginTransaction();
        foreach ($permissions as $permission) {
            if (! in_array($permission, $roleEnumClass::$permissionClass::all()->toArray())) {
                throw new InvalidArgumentException(
                    sprintf(
                        'The permission "%s" does not exist on the "%s" enum class.',
                        $permission,
                        $roleEnumClass::$permissionClass
                    )
                );
            }

            if (in_array($permission, $exitingPermissions)) {
                // If the permission already exists, we don't need to do anything.
                continue;
            }

            $bulkRolesToSave[] = new ModelPermission(['permission' => $permission]);
        }

        // Bulk insert the new permissions
        $this->model->modelPermissions()->saveMany($bulkRolesToSave ?? []);
        DB::commit();

        return true;
    }

    /**
     * Get the permissions.
     *
     * @return PermissionCollection
     */
    public function permissions(): PermissionCollection
    {
        return $this->rolesPermissions()->merge($this->directPermissions())->unique();
    }

    /**
     * Get the direct permissions.
     *
     * Permissions that are assigned to the model directly.
     *
     * @return PermissionCollection
     */
    public function directPermissions(): PermissionCollection
    {
        $roleEnumClass = $this->getRoleEnumClass();

        return $roleEnumClass::$permissionClass::collect(
            $this->model->modelPermissions->pluck('permission')->all()
        );
    }

    /**
     * Revoke permissions that were directly assigned to the model.
     *
     * @return bool
     */
    public function revoke(...$permissions): bool
    {
        if (empty($permissions)) {
            // Delete every permission if none was specified
            return $this->model->modelPermissions()->delete();
        }

        return $this->model->modelPermissions()->whereIn('permission', $permissions)->delete();
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
