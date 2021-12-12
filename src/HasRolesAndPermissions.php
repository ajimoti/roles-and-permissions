<?php

namespace Tarzancodes\RolesAndPermissions;

use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Contracts\RolesContract;
use Tarzancodes\RolesAndPermissions\Models\ModelRole;
use Tarzancodes\RolesAndPermissions\Repositories\ModelRepository;
use Tarzancodes\RolesAndPermissions\Repositories\PivotModelRepository;

trait HasRolesAndPermissions
{
    /**
     * Holds the repository to use
     *
     * @var RolesContract
     */
    private RolesContract $repository;

    /**
     * Boot trait
     * Set the repository to use
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->repository = new ModelRepository($this);
    }

    /**
     * Change the repository used to the pivot model repository
     *
     * @return self
     */
    public function of(Model $model, string $relationshipName = null): PivotModelRepository
    {
        return new PivotModelRepository($this, $model, $relationshipName);
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
        return $this->repository->can($permission, $arguments);
    }

    /**
     * Checks if the model has all the given permissions.
     *
     * @param string $permissions
     * @return bool
     */
    public function has(...$permissions): bool
    {
        return $this->repository->has(...$permissions);
    }

    /**
     * Checks if the model has all the given roles.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(...$roles): bool
    {
        return $this->repository->hasRole(...$roles);
    }

    /**
     * Get the model's roles.
     *
     * @return array
     */
    public function roles(): array
    {
        return $this->repository->roles();
    }

    /**
     * Get all the model's permissions.
     *
     * @return array
     */
    public function permissions(): array
    {
        return $this->repository->permissions();
    }

    /**
     * Assign the given role to the model.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function assign(...$roles): bool
    {
        return $this->repository->assign(...$roles);
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
            return $this->repository->removeRoles(func_get_args());
        }

        return $this->repository->removeRoles();
    }

    /**
     * Check if the model has a permission.
     *
     * @param string|int|array $permission
     * @return bool
     */
    public function authorize(...$permissions): bool
    {
        return $this->repository->authorize(...$permissions);
    }

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function authorizeRole(...$role): bool
    {
        return $this->repository->authorizeRole(...$role);
    }

    /**
     * Get the modelRoles relationship.
     */
    public function modelRoles()
    {
        return $this->morphMany(ModelRole::class, 'model');
    }
}
