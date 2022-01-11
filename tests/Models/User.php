<?php

namespace Ajimoti\RolesAndPermissions\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ajimoti\RolesAndPermissions\HasRoles;
use Ajimoti\RolesAndPermissions\Tests\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $guarded = [];

    protected $table = 'users';

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function merchants()
    {
        return $this->belongsToMany(Merchant::class);
    }
}
