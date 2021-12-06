<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tarzancodes\RolesAndPermissions\Exceptions\InvalidRelationName;

class Pivot
{
    protected string $roleColumnName;

    public function __construct(
        protected Model $localModel,
        protected Model $relatedModel,
        protected ?string $relationName = null,
        protected array $conditions = [],
    ) {
        $this->roleColumnName = config('roles-and-permissions.role_column_name');
    }

    public function getRelatedWithPivot(): ?Model
    {
        return $this->getRelationshipInstanceWithPivotQuery()->first();
    }

    public function getRelationshipInstanceWithPivotQuery(): BelongsToMany
    {
        $relationship = $this->getRelationshipInstance();

        // $this->conditions[] = [$relationship->getRelatedPivotKeyName(), $this->relatedModel->getKeyName()];

        return $relationship->wherePivot($relationship->getRelatedPivotKeyName(), $this->relatedModel->id);

        // return $relationship->wherePivot($this->conditions);
        // return $relationship->where($this->conditions);
        // return $relationship->where($relationship->getRelatedPivotKeyName(), $this->relatedModel->getKeyName());
    }

    public function getRelationshipInstance()
    {
        $roleColumnName = $this->roleColumnName;
        $relationName = $this->getRelationName();

        try {
            if ($this->localModel->{$relationName}() instanceof BelongsToMany) {
                // return $this->localModel->{$relationName}()->withPivot($roleColumnName);
                return $this->localModel->{$relationName}()->withPivot($roleColumnName)->withTimestamps();
            }

            throw new \InvalidArgumentException("The `{$relationName}` relation is not a BelongsToMany relation.");
        } catch (\Exception $exception) {
            if ($exception instanceof \BadMethodCallException) {
                $message = "The `{$relationName}` relation does not exist in model - " . $this->localModel::class . ".";
                $message .= isset($this->relationName) ? " Ensure the right relation name was passed" :
                                " Pass the right relation name as the second argument";

                throw new InvalidRelationName($message);
            }

            throw $exception;
        }
    }

    public function getRole()
    {
        $roleColumnName = $this->roleColumnName;

        return $this->getRelatedWithPivot()?->pivot->{$roleColumnName};
    }

    public function getRoleEnum(): string
    {
        $pivotTableName = $this->getPivotTableName();

        return config("roles-and-permissions.roles_enum.{$pivotTableName}") ??
                config('roles-and-permissions.roles_enum.users');
    }

    private function getRelationName(): string
    {
        if ($this->relationName) {
            return $this->relationName;
        }

        return $this->guessRelationName();
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
