<?php

namespace Tarzancodes\RolesAndPermissions\Tests\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Tarzancodes\RolesAndPermissions\HasRolesAndPermissions;
use Tarzancodes\RolesAndPermissions\Tests\Factories\UserFactory;

class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    // use HasFactory, Authenticatable, HasFactory, Authorizable;
    use HasFactory;
    use Authenticatable;
    use HasFactory;
    use HasRolesAndPermissions;

    protected $guarded = [];

    protected $table = 'users';

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
