<?php

namespace Ajimoti\RolesAndPermissions\Collections;

use Illuminate\Support\Collection;

class PermissionCollection extends Collection
{
    /**
     * Initialize class
     *
     * @return array
     */
    public function __construct(
        private array $permissions = [],
    ) {
        parent::__construct($permissions);
    }
}
