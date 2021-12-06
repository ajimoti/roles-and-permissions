<?php

namespace Tarzancodes\RolesAndPermissions\Concerns;

use Tarzancodes\RolesAndPermissions\Exceptions\PermissionDeniedException;

trait Authorizable
{
    public function authorize(...$permissions): bool
    {
        if ($this->has(...$permissions)) {
            return true;
        }

        // abort(403, 'You are not authorized to perform this action.');
        throw new PermissionDeniedException('You are not authorized to perform this action.');
    }
}
