<?php

namespace Ajimoti\RolesAndPermissions\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ModelPermission extends Pivot
{
    protected $guarded = [];

    /**
     * Get the modelable model that the role belongs to.
     */
    public function model()
    {
        return $this->morphTo();
    }
}
