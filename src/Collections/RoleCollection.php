<?php

namespace Tarzancodes\RolesAndPermissions\Collections;

use Illuminate\Support\Collection;

class RoleCollection extends Collection
{
    /**
     * Initialize class
     *
     * @return array
     */
    public function __construct(
        private array $roles,
    ) {
        parent::__construct($roles);
    }
}
