<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Concerns\Authorizable;

class PivotHasRoleAndPermissions
{
    use Authorizable;

    /**
     * Conditions for the "where" clause on the pivot table
     *
     * @var array
     */
    protected array $conditions = [];

    /**
     * An instance of the pivot model helper
     *
     * @var Pivot
     */
    protected Pivot $pivot;

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
        $this->pivot = new Pivot($localModel, $relatedModel, $relationName, $this->conditions);
        $this->roleColumnName = config('roles-and-permissions.role_column_name');
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
        $roleEnumClass = $this->pivot->roleEnumClass();

        if ($role = $this->pivot->role()) {
            return in_array($permission, $roleEnumClass::getPermissions($role));
        }

        return false;
    }

    /**
     * Checks if the model has the given permission.
     *
     * @param string $role
     * @return bool
     */
    public function has(...$permissions): bool
    {
        $roleEnumClass = $this->pivot->roleEnumClass();

        $permissions = collect($permissions)->flatten()->all();

        if ($role = $this->pivot->role()) {
            return $roleEnumClass::allPermissionsAreValid($role, $permissions);
        }

        return false;
    }

    /**
     * Checks if the model has the given role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string|int $role): bool
    {
        return $this->pivot->role() === $role;
    }

    /**
     * Get all the model's permissions.
     *
     * @return array
     */
    public function permissions(): array
    {
        $roleEnumClass = $this->pivot->roleEnumClass();

        if (empty($this->pivot->role())) {
            return [];
        }

        return $roleEnumClass::getPermissions($this->pivot->role());
    }

    /**
     * Assign the given role to the model.
     *
     * @param int|string $role
     * @return bool
     */
    public function assign(string $role): bool
    {
        $roleEnumClass = $this->pivot->roleEnumClass();

        if (! in_array($role, $roleEnumClass::getValues())) {
            throw new \InvalidArgumentException("The role `[{$role}]` does not exist.");
        }

        if ($this->pivot->relationshipInstanceWithPivotQuery()->exists()) {
            return $this->pivot->relationshipInstanceWithPivotQuery()->updateExistingPivot($this->relatedModel->id, [
                $this->roleColumnName => $role,
            ]);
        }

        $this->pivot->relationshipInstanceWithPivotQuery()->attach($this->relatedModel->id, [
            $this->roleColumnName => $role,
        ]);

        return true;
    }

    /**
     * Revoke the model's role.
     *
     * @return bool
     */
    public function removeRole(): bool
    {
        $roleEnumClass = $this->pivot->roleEnumClass();

        if ($roleEnumClass::deletePivotOnRemove()) {
            return $this->pivot->relationshipInstanceWithPivotQuery()->detach($this->relatedModel->id);
        }

        return $this->pivot->relationshipInstanceWithPivotQuery()->updateExistingPivot($this->relatedModel->id, [
            $this->roleColumnName => null,
        ]);
    }

    // public function where(string|array $column, $operator = null, $value = null): self
    // {
    //     if (is_array($column)) {
    //         $this->conditions = $column;
    //     } else {
    //         $this->conditions[] = [$column, $operator, $value];
    //     }

    //     return $this;
    // }
    public function where(Closure $closure)
    {
        $closure($this->pivot->relationshipInstance());
    }
}
