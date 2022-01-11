<?php

namespace Ajimoti\RolesAndPermissions\Exceptions;

use Exception;

class InvalidArgumentException extends Exception
{
    public function __construct($message = "expects at least one parameter")
    {
        parent::__construct($message);
    }
}
