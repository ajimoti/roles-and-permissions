<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pivot
{
    public function __construct(
        protected Model $localModel,
        protected Model $relatedModel,
        protected ?string $relationName = null,
        protected array $conditions = [],
    ){}

    public function getRelatedWithPivot(): Model
    {
        return $this->getRelatedWithPivotQuery()->first();
    }

    public function getRelatedWithPivotQuery(): BelongsToMany
    {
        $relationship = $this->getRelationshipInstance();

        $this->conditions[] = [$relationship->getRelatedPivotKeyName(), $this->relatedModel->getKeyName()];

        return $relationship->wherePivot($this->conditions);
        // return $relationship->where($this->conditions);
        // return $relationship->where($relationship->getRelatedPivotKeyName(), $this->relatedModel->getKeyName());
    }

    public function getRelationshipInstance()
    {
        $roleColumnName = config('roles-and-permissions.role_column_name');
        $relationName = $this->getRelationName();

        if ($this->localModel->{$relationName}() instanceof BelongsToMany) {
            return $this->localModel->{$relationName}()->withPivot($roleColumnName);
        }

        throw new \InvalidArgumentException("The `{$relationName}` relation is not a BelongsToMany relation.");
    }

    public function getRole()
    {
        $roleColumnName = config('roles-and-permissions.role_column_name');

        return $this->getRelatedWithPivot()->{$roleColumnName};
    }

    public function getRoleEnum(): BaseRole
    {
        $pivotTableName = $this->getPivotTableName();

        return config("roles-and-permissions.role_enum.{$pivotTableName}") ??
                config('roles-and-permissions.roles_enum.users');
    }

    private function getRelationName(): string
    {
        if ($this->relationName) {
            return $this->relationName;
        }

        $this->relationName = $this->guessRelationName();

        return $this->relationName;
    }

    private function guessRelationName(): string
    {
        return Str::of($this->relatedModel->getTable())->camel()->plural();
    }

    private function getPivotTableName()
    {
        return $this->getRelationshipInstance()->getTable();
    }
}
