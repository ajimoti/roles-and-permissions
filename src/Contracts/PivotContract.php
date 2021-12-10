<?php

namespace Tarzancodes\RolesAndPermissions\Contracts;

interface PivotContract
{
    /**
     * Set intermediate table (pivot table) columns when assigning a role.
     *
     * @return self
     */
    public function withPivot(array $columns): self;
}
