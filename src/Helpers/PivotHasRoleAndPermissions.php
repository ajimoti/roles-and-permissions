<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\Concerns\Authorizable;

class PivotHasRoleAndPermissions
{
    use Authorizable;

    /**
     * Methods used to filter results when defining a 'belongsToMany' relationship
     *
     * @var array
     */
    public const CONDITIONAL_METHODS = [
        'wherePivot',
        'wherePivotIn',
        'wherePivotNotIn',
        'wherePivotBetween',
        'wherePivotNotBetween',
        'wherePivotNull',
        'wherePivotNotNull',
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

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (in_array($method, self::CONDITIONAL_METHODS)) {
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
