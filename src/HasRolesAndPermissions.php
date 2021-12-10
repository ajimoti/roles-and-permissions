<?php

namespace Tarzancodes\RolesAndPermissions;

use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Contracts\RolesContract;
use Tarzancodes\RolesAndPermissions\Models\ModelRole;
use Tarzancodes\RolesAndPermissions\Supports\ModelSupport;
use Tarzancodes\RolesAndPermissions\Supports\PivotModelSupport;

trait HasRolesAndPermissions
{
    private RolesContract $support;

    protected function __construct()
    {
        $this->support = new ModelSupport($this);
    }

    /**
     * A model may have multiple roles.
     *
     * @return self
     */
    public function of(Model $model, string $relationshipName = null): self
    {
        $this->support = new PivotModelSupport($this, $model, $relationshipName);

        return $this;
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
        return $this->support->can($permission, $arguments);
    }

    /**
     * Checks if the model has all the given permissions.
     *
     * @param string $permissions
     * @return bool
     */
    public function has(...$permissions): bool
    {
        return $this->support->has(...$permissions);
    }

    /**
     * Checks if the model has all the given roles.
     *
     * @param string $role
     * @return bool
     */
    public function hasRoles(...$roles): bool
    {
        return $this->support->hasRoles(...$roles);
    }

    /**
     * Get the model's roles.
     *
     * @return array
     */
    public function roles(): array
    {
        return $this->support->roles();
    }

    /**
     * Get all the model's permissions.
     *
     * @return array
     */
    public function permissions(): array
    {
        return $this->support->permissions();
    }

    /**
     * Assign the given role to the model.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function assign(...$roles): bool
    {
        return $this->support->assign(...$roles);
    }

    /**
     * Remove the model's role.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function removeRoles(): bool
    {
        if (count(func_get_args())) {
            return $this->support->removeRoles(func_get_args());
        }

        return $this->support->removeRoles();
    }

    /**
     * Check if the model has a permission.
     *
     * @param string|int|array $permission
     * @return bool
     */
    public function authorize(...$permissions): bool
    {
        return $this->support->authorize(...$permissions);
    }

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function authorizeRole(...$role): bool
    {
        return $this->support->authorizeRole(...$role);
    }

    /**
     * Get the modelRoles relationship.
     */
    public function modelRoles()
    {
        return $this->morphMany(ModelRole::class, 'model');
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // If the model is a pivot relationship,
        // we will call the magic method on the pivot model support
        if ($this->support instanceof PivotModelSupport) {
            $this->support->{$method}(...$parameters);

            return $this;
        }

        // Use the model's magic method
        return parent::__call($method, $parameters);
    }

    // /**
    //  * Get the name of the "role" column.
    //  *
    //  * @return string
    //  */
    // private function getRoleColumnName(): string
    // {
    //     return config('roles-and-permissions.pivot.column_name');
    // }

    // /**
    //  * Get the name of the "role" enum class.
    //  *
    //  * @return string
    //  */
    // private function getRoleEnumClass(): string
    // {
    //     return config('roles-and-permissions.roles_enum.default');
    // }

    // /**
    //  * Get the model's roles
    //  *
    //  * @return array
    //  */
    // protected function getRoles(): array
    // {
    //     return $this->modelRoles()->pluck($this->getRoleColumnName())->all();
    // }
}
