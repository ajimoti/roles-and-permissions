<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Concerns\Authorizable;
use Tarzancodes\RolesAndPermissions\Contracts\HasRoleContract;

class PivotRelation implements HasRoleContract
{
    use Authorizable;

    protected array $conditions = [];

    public function __construct(
        protected Model $localModel,
        protected Model $relatedModel,
        protected ?string $relationName = null
    ) {
        $this->pivot = new Pivot($localModel, $relatedModel, $relationName, $this->conditions);
    }

    // can accept a permission, an array of permissions, or multiple parameters
    // public function can($permission)
    public function can($permission, $arguments = [])
    {
        $roleEnum = config('roles-and-permissions.roles_enum.users');

        if ($role = $this->pivot->getRole()) {
            return in_array($permission, $roleEnum::getPermissions($role));
        }

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

        return $roleEnum::getPermissions($this->pivot->getRole());
    }

    public function assign(string $role): bool
    {
        $roleEnum = $this->pivot->getRoleEnum();

        if (! in_array($role, $roleEnum::getValues())) {
            throw new \InvalidArgumentException("The role {$role} does not exist.");
        }

        return $this->pivot->getRelationshipInstance()->updateExistingPivot($this->relatedModel->id, [
            config('roles-and-permissions.role_column_name') => $role,
        ]);
    }

    public function removeRole(): bool
    {
        return $this->pivot->getRelationshipInstance()->updateExistingPivot($this->relatedModel->id, [
            config('roles-and-permissions.role_column_name') => null,
        ]);
    }

    public function where(string|array $column, $operator = null, $value = null): self
    {
        if (is_array($column)) {
            $this->conditions = $column;
        } else {
            $this->conditions[] = [$column, $operator, $value];
        }

        return $this;
    }
}
