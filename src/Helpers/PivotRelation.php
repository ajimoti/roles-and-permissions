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
        $roleEnum = $this->pivot->getRoleEnum();

        if ($role = $this->pivot->getRole()) {
            return in_array($permission, $roleEnum::getPermissions($role));
        }
        // dd($roleEnum, $role);
        return false;
    }

    public function has(...$permissions): bool
    {
        $roleEnum = $this->pivot->getRoleEnum();

        $permissions = collect($permissions)->flatten()->all();

        if ($role = $this->pivot->getRole()) {
            return $roleEnum::allPermissionsAreValid($role, $permissions);
        }

        return false;
    }

    public function permissions(): array
    {
        $roleEnum = $this->pivot->getRoleEnum();

        if (empty($this->pivot->getRole())) {
            return [];
        }

        return $roleEnum::getPermissions($this->pivot->getRole());
    }

    public function assign(string $role): bool
    {
        $roleEnum = $this->pivot->getRoleEnum();

        if (! in_array($role, $roleEnum::getValues())) {
            throw new \InvalidArgumentException("The role `{$role}` does not exist.");
        }

        if ($this->pivot->getRelationshipInstanceWithPivotQuery()->exists()) {
            return $this->pivot->getRelationshipInstanceWithPivotQuery()->updateExistingPivot($this->relatedModel->id, [
                $this->roleColumnName => $role,
            ]);
        }

        $this->pivot->getRelationshipInstanceWithPivotQuery()->attach($this->relatedModel->id, [
            $this->roleColumnName => $role,
        ]);

        return true;
    }

    public function removeRole(): bool
    {
        $roleEnum = $this->pivot->getRoleEnum();

        if ($roleEnum::deletePivotOnRemove()) {
            return $this->pivot->getRelationshipInstanceWithPivotQuery()->detach($this->relatedModel->id);
        }

        return $this->pivot->getRelationshipInstanceWithPivotQuery()->updateExistingPivot($this->relatedModel->id, [
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
        $closure($this->pivot->getRelationshipInstance());
    }
}
