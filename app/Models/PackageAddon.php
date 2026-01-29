<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PackageAddon extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'package_addons';
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
