<?php

namespace Ajimoti\RolesAndPermissions\Contracts;

use Ajimoti\RolesAndPermissions\Collections\PermissionCollection;

interface DirectPermissionsContract
{
    /**
     * Give permissions to the model.
     *
     * @return bool
     */
    public function give(): bool;

    /**
     * Get the permissions that are directly assigned to the model.
     *
     * @return PermissionCollection
     */
    public function directPermissions(): PermissionCollection;

    /**
     * Remove permissions that were directly assigned to the model.
     *
     * @return bool
     */
    public function revoke(): bool;
}
