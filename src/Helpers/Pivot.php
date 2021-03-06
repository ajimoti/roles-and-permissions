<?php

namespace Ajimoti\RolesAndPermissions\Helpers;

use Ajimoti\RolesAndPermissions\Collections\RoleCollection;
use Ajimoti\RolesAndPermissions\Concerns\HasRoles;
use Ajimoti\RolesAndPermissions\Exceptions\InvalidRelationNameException;
use BadMethodCallException;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Pivot
{
    use HasRoles;

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
    }

    /**
     * Get the related models with pivot attributes.
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function getRelatedModelsWithPivot(): ?Collection
    {
        return $this->relationshipInstanceAsQuery()->get();
    }

    /**
     * Get the relationship instance with conditions set as a query.
     *
     * @return BelongsToMany
     */
    public function relationshipInstanceAsQuery(): BelongsToMany
    {
        $relationship = $this->relationshipInstance();

        $query = $relationship->wherePivot($relationship->getRelatedPivotKeyName(), $this->relatedModel->getKey())
            ->wherePivotNotNull($this->getRoleColumnName());

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
        $roleColumnName = $this->getRoleColumnName();
        $relationName = $this->getRelationshipName();

        try {
            $relationshipInstance = $this->localModel->{$relationName}();
            if ($relationshipInstance instanceof BelongsToMany) {
                return $relationshipInstance->withPivot($roleColumnName)->withTimestamps();
            }

            throw new InvalidArgumentException("The `{$relationName}` relation is not a BelongsToMany relation.");
        } catch (Exception $exception) {
            if ($exception instanceof BadMethodCallException) {
                $message = "`{$relationName}` relationship does not exist in model [" . $this->localModel::class . "].";
                $message .= isset($this->relationName) ? " Ensure the right relation name was passed" :
                                " Pass the right relation name as the second argument";

                throw new InvalidRelationNameException($message);
            }

            throw $exception;
        }
    }

    /**
     * Get the roles.
     *
     * @return RoleCollection
     */
    public function getRoles(): RoleCollection
    {
        $roleClass = $this->getRoleEnumClass();

        foreach ($this->getRelatedModelsWithPivot() as $model) {
            $role = $model->pivot->{$this->getRoleColumnName()};

            // Cast the roles to the correct type
            // This is needed because the roles are stored as strings in the database
            if (is_numeric($role)) {
                $role = (int) $role;
            }

            $cleanRoles[] = new $roleClass($role);
        }

        return new RoleCollection($cleanRoles ?? []);
    }

    /**
     * Get the name of the "role" enum class.
     *
     * @return string
     */
    public function getRoleEnumClass(): string
    {
        $pivotTableName = $this->getPivotTableName();

        return config("roles-and-permissions.roles_enum.{$pivotTableName}") ??
                config('roles-and-permissions.roles_enum.default');
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
