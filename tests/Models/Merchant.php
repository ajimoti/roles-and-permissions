<?php

namespace Ajimoti\RolesAndPermissions\Tests\Models;

use Ajimoti\RolesAndPermissions\HasRoles;
use Ajimoti\RolesAndPermissions\Tests\Factories\MerchantFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
