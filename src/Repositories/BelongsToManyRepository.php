<?php

namespace Ajimoti\RolesAndPermissions\Repositories;

use Ajimoti\RolesAndPermissions\Collections\PermissionCollection;
use Ajimoti\RolesAndPermissions\Collections\RoleCollection;
use Ajimoti\RolesAndPermissions\Concerns\Authorizable;
use Ajimoti\RolesAndPermissions\Contracts\PivotContract;
use Ajimoti\RolesAndPermissions\Contracts\RolesContract;
use Ajimoti\RolesAndPermissions\Facades\Check;
use Ajimoti\RolesAndPermissions\Helpers\BasePermission;
use Ajimoti\RolesAndPermissions\Helpers\Pivot;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BelongsToManyRepository implements RolesContract, PivotContract
{
    use Authorizable;

    /**
     * Methods used to filter results when defining a 'belongsToMany' relationship
     *
     * @var array
     */
    public const CONDITIONAL_METHOD_PREFIXES = [
        'where',
        'orWhere',
    ];

    /**
     * An instance of the pivot table helper
     *
     * @var Pivot
     */
    protected Pivot $pivot;

    /**
     * Pivot columns to set when assigning a role
     *
     * @var array
     */
    protected array $pivotData = [];

    /**
     * The name of the "role" column on the pivot table.
     *
     * @var string
     */
    protected string $roleColumnName;

    /**
     * Boot pivot relationship
     *
     * @param Model $localModel
     * @param Model $relatedModel
     * @param string|null $relationName
     */
    public function __construct(
        protected Model $localModel,
        protected Model $relatedModel,
        protected ?string $relationName = null
    ) {
        $this->pivot = new Pivot($localModel, $relatedModel, $relationName);
        $this->roleColumnName = config('roles-and-permissions.column_name');
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

        return in_array($permission, $this->pivot->permissions()->toArray());
    }

    /**
     * Checks if the model has the given permission.
     *
     * @param string|int|array $permissions
     * @return bool
     */
    public function holds(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten()->toArray();

        if (empty($permissions) || empty($permissions[0])) {
            throw new InvalidArgumentException();
        }

        return Check::all($permissions)->existsIn($this->pivot->permissions()->toArray());
    }

    /**
     * Checks if the model has the given role.
     *
     * @param string|int|array $role
     * @return bool
     */
    public function hasRole(...$roles): bool
    {
        $roles = collect($roles)->flatten()->toArray();

        return Check::all($roles)->existsIn($this->pivot->getRoles()->toArray());
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
        return $this->pivot->getRoles();
    }

    /**
     * Get all the model's permissions.
     *
     * @return PermissionCollection
     */
    public function permissions(): PermissionCollection
    {
        return $this->pivot->permissions();
    }

    /**
     * Assign the given role to the model.
     *
     * @param int|string $role
     * @return bool
     */
    public function assign(...$roles): bool
    {
        $roles = collect($roles)->flatten()->toArray();
        $roleEnumClass = $this->pivot->getRoleEnumClass();

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

            $pivotTableData = array_merge([$this->roleColumnName => $role], $this->pivotData);
            $roleExists = $this->pivot->relationshipInstance()->wherePivot($this->roleColumnName, $role)->exists();

            // If the model is already assigned the role, and the pivot data isset,
            // we proceed to update the pivot table we the new data.
            if ($roleExists && ! empty($this->pivotData)) {
                $this->pivot->relationshipInstance()->updateExistingPivot($this->relatedModel->getKey(), $pivotTableData);
            }

            if (! $roleExists) {
                // Assign the role to the model, and attach the pivot data.
                $this->pivot->relationshipInstance()->attach($this->relatedModel->getKey(), $pivotTableData);
            }
        }

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
        $roles = empty(func_get_args()) ? $this->pivot->getRoles()->toArray() : func_get_args();
        $roleEnumClass = $this->pivot->getRoleEnumClass();

        $query = $this->pivot->relationshipInstanceAsQuery()
                    ->wherePivotIn($this->roleColumnName, $roles);

        if ($roleEnumClass::deletePivotOnRemove()) {
            return $query->detach();
        }

        return $query->updateExistingPivot($this->relatedModel->id, [$this->roleColumnName => null]);
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
     * Columns to set when assigning a role.
     *
     * @param array $columnsAndValues
     * @return self
     */
    public function withPivot(array $columnsAndValues): self
    {
        $this->pivotData = array_merge($this->pivotData, $columnsAndValues);

        return $this;
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
        if (Str::startsWith($method, self::CONDITIONAL_METHOD_PREFIXES) ||
            method_exists($this->relatedModel, 'scope' . Str::ucfirst($method))) {
            $this->pivot->appendCondition($method, $parameters);

            return $this;
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()',
            static::class,
            $method
        ));
    }
}
