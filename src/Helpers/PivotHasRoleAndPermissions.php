<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Facades\Check;
use Tarzancodes\RolesAndPermissions\Concerns\Authorizable;

class PivotHasRoleAndPermissions
{
    use Authorizable;

    /**
     * Methods used to filter results when defining a 'belongsToMany' relationship
     *
     * @var array
     */
    const CONDITIONAL_METHOD_PREFIXES = [
        'where',
        'orWhere',
    ];

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
        $this->pivot = new Pivot($localModel, $relatedModel, $relationName);
        $this->roleColumnName = config('roles-and-permissions.pivot.role_column_name');
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
        return in_array($permission, $this->pivot->permissions());
    }

    /**
     * Checks if the model has the given permission.
     *
     * @param array|string|int $permissions
     * @return bool
     */
    public function has(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten()->all();

        return Check::forAll($permissions)->in($this->pivot->permissions());
    }

    /**
     * Checks if the model has the given role.
     *
     * @param array|string|int $role
     * @return bool
     */
    public function hasRole(...$roles): bool
    {
        $roles = collect($roles)->flatten()->all();

        return Check::forAll($roles)->in($this->pivot->roles());
    }

    /**
     * Get all the model's permissions.
     *
     * @return array
     */
    public function permissions(): array
    {
        return $this->pivot->permissions();
    }

    /**
     * Assign the given role to the model.
     *
     * @param int|string $role
     * @return bool
     */
    public function assign(string $role, array $pivotData): bool
    {
        $roleEnumClass = $this->pivot->roleEnumClass();

        if (! in_array($role, $roleEnumClass::getValues())) {
            throw new \InvalidArgumentException("The role `[{$role}]` does not exist.");
        }

        if ($this->pivot->relationshipInstanceWithPivotQuery()->wherePivot($this->roleColumnName, $role)->exists()) {
            return $this->pivot->relationshipInstanceWithPivotQuery()->updateExistingPivot($this->relatedModel->id, [
                $this->roleColumnName => $role,
            ]);
        }

        $this->pivot->relationshipInstance()->attach(
            $this->relatedModel->id, array_merge([$this->roleColumnName => $role], $pivotData)
        );

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

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, self::CONDITIONAL_METHOD_PREFIXES)) {
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
