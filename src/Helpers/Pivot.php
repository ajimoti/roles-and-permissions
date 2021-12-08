<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tarzancodes\RolesAndPermissions\Exceptions\InvalidRelationName;

class Pivot
{
    /**
     * The name of the "role" column on the pivot table.
     *
     * @var string
     */
    protected string $roleColumnName;

    /**
     * Conditions for the "where" clause on the pivot table
     *
     * @var array
     */
    protected array $conditions = [];

    /**
     * Boot pivot relationship
     *
     * @param Model $localModel
     * @param Model $relatedModel
     * @param string|null $relationName
     * @param array $conditions
     */
    public function __construct(
        protected Model $localModel,
        protected Model $relatedModel,
        protected ?string $relationName = null,
    ) {
        $this->roleColumnName = config('roles-and-permissions.pivot.role_column_name');
    }

    /**
     * Get the related models with pivot attributes.
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getRelatedModelsWithPivot(): ?Collection
    {
        return $this->relationshipInstanceWithPivotQuery()->get();
    }

    /**
     * Get the relationship instance with pivot attributes.
     *
     * @return BelongsToMany
     */
    public function relationshipInstanceWithPivotQuery(): BelongsToMany
    {
        $relationship = $this->relationshipInstance();

        $query = $relationship->wherePivot($relationship->getRelatedPivotKeyName(), $this->relatedModel->getKey());

        foreach ($this->conditions as $condition) {
            $query->{$condition['method_name']}(...$condition['parameters']);
        }

        return $query;
    }

    /**
     * Get the relationship instance.
     *
     * @return BelongsToMany
     */
    public function relationshipInstance(): BelongsToMany
    {
        $roleColumnName = $this->roleColumnName;
        $relationName = $this->getRelationshipName();

        try {
            $relationshipInstance = $this->localModel->{$relationName}();
            if ($relationshipInstance instanceof BelongsToMany) {
                return $relationshipInstance->withPivot($roleColumnName)->withTimestamps();
            }

            throw new \InvalidArgumentException("The `{$relationName}` relation is not a BelongsToMany relation.");
        } catch (\Exception $exception) {
            if ($exception instanceof \BadMethodCallException) {
                $message = "`{$relationName}` relation does not exist in model [" . $this->localModel::class . "].";
                $message .= isset($this->relationName) ? " Ensure the right relation name was passed" :
                                " Pass the right relation name as the second argument";

                throw new InvalidRelationName($message);
            }

            throw $exception;
        }
    }

    /**
     * Get the roles.
     *
     * @return array
     */
    public function roles(): array
    {
        foreach ($this->getRelatedModelsWithPivot() as $model) {
            $roles[] = $model->pivot->{$this->roleColumnName};
        }

        return $roles ?? [];
    }

    /**
     * Get the permissions.
     *
     * @return array
     */
    public function permissions(): array
    {
        $roleEnumClass = $this->roleEnumClass();

        $allPermissions = [];
        foreach ($this->roles() as $role) {
            $allPermissions = array_merge($allPermissions, $roleEnumClass::getPermissions($role));
        }

        return $allPermissions;
    }

    /**
     * Get the name of the "role" enum class.
     *
     * @return string
     */
    public function roleEnumClass(): string
    {
        $pivotTableName = $this->getPivotTableName();

        return config("roles-and-permissions.roles_enum.{$pivotTableName}") ??
                config('roles-and-permissions.roles_enum.users');
    }

    /**
     * Append a condition to the "where" clause on the pivot table.
     *
     * @return void
     */
    public function appendCondition(string $method, array $parameters): void
    {
        $this->conditions[] = [
            'method_name' => $method,
            'parameters' => $parameters,
        ];
    }

    /**
     * Get the relationship name on the local model
     *
     * @return string
     */
    private function getRelationshipName(): string
    {
        if ($this->relationName) {
            return $this->relationName;
        }

        return $this->guessRelationshipName();
    }

    /**
     * Guess the relationship name on the local model
     *
     * @return string
     */
    private function guessRelationshipName(): string
    {
        return Str::of($this->relatedModel->getTable())->camel()->plural();
    }

    /**
     * Get the pivot table name
     *
     * @return string
     */
    private function getPivotTableName(): string
    {
        return $this->relationshipInstance()->getTable();
    }
}
