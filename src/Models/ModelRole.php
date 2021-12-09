<?php

namespace Tarzancodes\RolesAndPermissions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ModelRole extends Pivot
{
    // use HasFactory;

    protected $guarded = [];

    /**
     * Get the modelable model that the role belongs to.
     */
    public function model()
    {
        return $this->morphTo();
    }
}
