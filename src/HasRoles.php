<?php

namespace Ajimoti\RolesAndPermissions;

use Ajimoti\RolesAndPermissions\Collections\PermissionCollection;
use Ajimoti\RolesAndPermissions\Collections\RoleCollection;
use Ajimoti\RolesAndPermissions\Exceptions\InvalidArgumentException;
use Ajimoti\RolesAndPermissions\Helpers\BasePermission;
use Ajimoti\RolesAndPermissions\Models\ModelRole;
use Ajimoti\RolesAndPermissions\Repositories\BelongsToManyRepository;
use Ajimoti\RolesAndPermissions\Repositories\ModelRepository;
use Ajimoti\RolesAndPermissions\Concerns\HasDirectPermissions;
use Ajimoti\RolesAndPermissions\Concerns\InteractsWithModel;
use Ajimoti\RolesAndPermissions\Concerns\SupportsMagicCalls;
use Illuminate\Database\Eloquent\Model;

trait HasRoles
{
    use InteractsWithModel;
    use HasDirectPermissions;
    use SupportsMagicCalls;

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

        return $this->repository()->can($permission, $arguments);
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

        return $this->repository()->holds(...$permissions);
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

        return $this->repository()->hasRole(...$roles);
    }

    /**
     * Checks if the model has all the given roles.
     *
     * @param string|int|array|RoleCollection $role
     * @return bool
     */
    public function hasRoles(...$roles): bool
    {
        $roles = collect($roles)->flatten()->toArray();

        return $this->repository()->hasRoles(...$roles);
    }

    /**
     * Get the model's roles.
     *
     * @return RoleCollection
     */
    public function roles(): RoleCollection
    {
        return $this->repository()->roles();
    }

    /**
     * Get all the model's permissions.
     *
     * @return PermissionCollection
     */
    public function permissions(): PermissionCollection
    {
        return $this->repository()->permissions();
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

        return $this->repository()->assign(...$roles);
    }

    /**
     * Remove the model's role.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function removeRoles(): bool
    {
        return $this->repository()->removeRoles(...func_get_args());
    }

    /**
     * Remove the model's role.
     *
     * @param string|int|array $roles
     * @return bool
     */
    public function removeRole(...$role): bool
    {
        $role = collect($role)->flatten()->toArray();

        return $this->repository()->removeRole(...$role);
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

        return $this->repository()->authorize(...$permissions);
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

        return $this->repository()->authorizeRole(...$role);
    }

    /**
     * Check if the model has a role.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function authorizeRole(...$role): bool
    {
        $role = collect($role)->flatten()->toArray();

        return $this->repository()->authorizeRole($role);
    }

    /**
     * Get the modelRoles relationship.
     */
    public function modelRoles()
    {
        return $this->morphMany(ModelRole::class, 'model');
    }

    /**
     * Get model repository
     *
     * @return ModelRepository
     */
    public function repository(): ModelRepository
    {
        return new ModelRepository($this);
    }

    public function __call($method, $parameters)
    {
        if ($this->isPossibleMagicCall($method)) {
            return $this->performMagic(
                $method,
                $this->repository()->getRoleEnumClass()
            );
        }

        return parent::__call($method, $parameters);
    }
}
