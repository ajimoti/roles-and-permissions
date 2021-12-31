<?php

namespace Tarzancodes\RolesAndPermissions\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tarzancodes\RolesAndPermissions\HasRoles;
use Tarzancodes\RolesAndPermissions\Tests\Factories\MerchantFactory;

class Merchant extends Model
{
    use HasFactory;
    use HasRoles;

    protected $guarded = [];

    protected $table = 'merchants';

    protected static function newFactory()
    {
        return MerchantFactory::new();
    }

    public function merchantUsers()
    {
        return $this->belongsToMany(User::class);
    }
}
