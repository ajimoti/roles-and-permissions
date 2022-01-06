<?php

namespace Tarzancodes\RolesAndPermissions;

use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Collections\PermissionCollection;
use Tarzancodes\RolesAndPermissions\Collections\RoleCollection;
use Tarzancodes\RolesAndPermissions\Contracts\RolesContract;
use Tarzancodes\RolesAndPermissions\Exceptions\InvalidArgumentException;
use Tarzancodes\RolesAndPermissions\Helpers\BasePermission;
use Tarzancodes\RolesAndPermissions\Models\ModelRole;
use Tarzancodes\RolesAndPermissions\Repositories\ModelRepository;
use Tarzancodes\RolesAndPermissions\Repositories\BelongsToManyRepository;

trait HasRoles
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
     * Change the repository used to the pivot table repository
     *
     * @return self
     */
    public function of(Model $model, string $relationshipName = null): BelongsToManyRepository
    {
        return new BelongsToManyRepository($this, $model, $relationshipName);
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
        if ($permission instanceof BasePermission) {
            $permission = $permission->value;
        }

        return $this->repository->can($permission, $arguments);
    }

    /**
     * Checks if the model has all the given permissions.
     *
     * @param string|int|array|PermissionCollection $permissions
     * @return bool
     */
    public function holds(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten()->toArray();

        if (empty($permissions) || empty($permissions[0])) {
            throw new InvalidArgumentException();
        }

        return $this->repository->holds(...$permissions);
    }

    /**
     * Checks if the model has all the given roles.
     *
     * @param string|int|array|RoleCollection $role
     * @return bool
     */
    public function hasRole(...$roles): bool
    {
        $roles = collect($roles)->flatten()->toArray();

        return $this->repository->hasRole(...$roles);
    }

    /**
     * Checks if the model has all the given roles.
     *
     * @param string|int|array|RoleCollection $role
     * @return bool
     */
    public function hasRoles(...$roles): bool
    {
        return $this->hasRole(...$roles);
    }

    /**
     * Get the model's roles.
     *
     * @return RoleCollection
     */
    public function roles(): RoleCollection
    {
        return $this->repository->roles();
    }

    /**
     * Get all the model's permissions.
     *
     * @return PermissionCollection
     */
    public function permissions(): PermissionCollection
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
        $roles = collect($roles)->flatten()->toArray();

        if (empty($roles)) {
            throw new InvalidArgumentException();
        }

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
        return $this->repository->removeRoles(...func_get_args());
    }

    /**
     * Remove the model's role.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function removeRole(...$role): bool
    {
        return $this->removeRoles(...$role);
    }

    /**
     * Check if the model has a permission.
     *
     * @param string|int|array $permission
     * @return bool
     */
    public function authorize(...$permissions): bool
    {
        if (empty($permissions) || empty($permissions[0])) {
            throw new InvalidArgumentException();
        }

        return $this->repository->authorize(...$permissions);
    }

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function authorizeRoles(...$role): bool
    {
        if (empty($role) || empty($role[0])) {
            throw new InvalidArgumentException();
        }

        return $this->repository->authorizeRole(...$role);
    }

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function authorizeRole(...$role): bool
    {
        return $this->authorizeRoles(...$role);
    }

    /**
     * Get the modelRoles relationship.
     */
    public function modelRoles()
    {
        return $this->morphMany(ModelRole::class, 'model');
    }

    /**
     * Get current repository
     *
     * Used strictly for testing
     *
     * @return RolesContract
     */
    public function getRepository(): RolesContract
    {
        return $this->repository;
    }
}
