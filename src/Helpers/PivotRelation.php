<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Concerns\Authorizable;
use Tarzancodes\RolesAndPermissions\Contracts\HasRoleContract;

class PivotRelation implements HasRoleContract
{
    use Authorizable;

    protected array $conditions = [];

    protected Pivot $pivot;

    protected string $roleColumnName;

    public function __construct(
        protected Model $localModel,
        protected Model $relatedModel,
        protected ?string $relationName = null
    ) {
        $this->pivot = new Pivot($localModel, $relatedModel, $relationName, $this->conditions);
        $this->roleColumnName = config('roles-and-permissions.role_column_name');
    }

    // can accept a permission, an array of permissions, or multiple parameters
    // public function can($permission)
    public function can($permission, $arguments = [])
    {
        $roleEnumClass = $this->pivot->roleEnumClass();

        if ($role = $this->pivot->role()) {
            return in_array($permission, $roleEnumClass::getPermissions($role));
        }

        return false;
    }

    public function has(...$permissions): bool
    {
        $roleEnumClass = $this->pivot->roleEnumClass();

        $permissions = collect($permissions)->flatten()->all();

        if ($role = $this->pivot->role()) {
            return $roleEnumClass::allPermissionsAreValid($role, $permissions);
        }

        return false;
    }

    public function permissions(): array
    {
        $roleEnumClass = $this->pivot->roleEnumClass();

        if (empty($this->pivot->role())) {
            return [];
        }

        return $roleEnumClass::getPermissions($this->pivot->role());
    }

    public function assign(string $role): bool
    {
        $roleEnumClass = $this->pivot->roleEnumClass();

        if (! in_array($role, $roleEnumClass::getValues())) {
            throw new \InvalidArgumentException("The role `{$role}` does not exist.");
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
