<?php

namespace Tarzancodes\RolesAndPermissions\Helpers;

use BenSampo\Enum\Enum;

class BasePermission extends Enum
{
    public function all(): array
    {
        return $this->getValues();
    }
}
