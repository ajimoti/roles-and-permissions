<?php

namespace Tarzancodes\RolesAndPermissions\Tests\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tarzancodes\RolesAndPermissions\HasRolesAndPermissions;
use Tarzancodes\RolesAndPermissions\Tests\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPermissions;

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
