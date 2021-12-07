<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
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
        protected array $conditions = [],
    ) {
        $this->roleColumnName = config('roles-and-permissions.role_column_name');
    }

    /**
     * Get the related model with pivot attributes.
     *
     * @return Model|null
     */
    public function getRelatedModelWithPivot(): ?Model
    {
        return $this->relationshipInstanceWithPivotQuery()->first();
    }

    /**
     * Get the relationship instance with pivot attributes.
     *
     * @return BelongsToMany
     */
    public function relationshipInstanceWithPivotQuery(): BelongsToMany
    {
        $relationship = $this->relationshipInstance();

        // $this->conditions[] = [$relationship->getRelatedPivotKeyName(), $this->relatedModel->getKeyName()];

        return $relationship->wherePivot($relationship->getRelatedPivotKeyName(), $this->relatedModel->id);

        // return $relationship->wherePivot($this->conditions);
        // return $relationship->where($this->conditions);
        // return $relationship->where($relationship->getRelatedPivotKeyName(), $this->relatedModel->getKeyName());
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
     * Get the role.
     *
     * @return string|null
     */
    public function role(): ?string
    {
        return $this->getRelatedModelWithPivot()?->pivot->{$this->roleColumnName};
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
