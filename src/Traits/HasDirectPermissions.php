<?php

namespace Ajimoti\RolesAndPermissions\Traits;

use Ajimoti\RolesAndPermissions\Collections\PermissionCollection;
use Ajimoti\RolesAndPermissions\Models\ModelPermission;
use Ajimoti\RolesAndPermissions\Repositories\ModelRepository;

trait HasDirectPermissions
{
    /**
     * Get model repository
     *
     * @return ModelRepository
     */
    abstract public function repository(): ModelRepository;

    /**
     * Give a model direct permissions.
     *
     * @param string|int|array $permissions
     * @return bool
     */
    public function give(...$permissions): bool
    {
        $permissions = collect($permissions)->flatten()->toArray();

        return $this->repository()->give(...$permissions);
    }

    /**
     * Get the model's direct permissions.
     *
     * @return PermissionCollection
     */
    public function directPermissions(): PermissionCollection
    {
        return $this->repository()->directPermissions();
    }

    /**
     * Revoke a model permissions.
     *
     * @param string|int|array $permissions
     * @return bool
     */
    public function revoke()
    {
        $permissions = collect(func_get_args())->flatten()->toArray();

        return $this->repository()->revoke(...$permissions);
    }

    /**
     * Get the modelPermissions relationship.
     */
    public function modelPermissions()
    {
        return $this->morphMany(ModelPermission::class, 'model');
    }
}
