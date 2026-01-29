<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\LogsActivity;

class PackageCategory extends Model
{
    use HasFactory, LogsActivity;

    protected $primaryKey = 'category_id';
    
    protected $fillable = [
        'name',
        'description',
        'color_code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the packages for this category.
     */
    public function packages()
    {
        return $this->hasMany(Package::class, 'category_id', 'category_id');
    }

    /**
     * Get active packages for this category.
     */
    public function activePackages()
    {
        return $this->packages()->where('is_active', true);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the count of packages in this category.
     */
    public function getPackageCountAttribute()
    {
        return $this->packages()->count();
    }

    /**
     * Get the count of active packages in this category.
     */
    public function getActivePackageCountAttribute()
    {
        return $this->activePackages()->count();
    }
}