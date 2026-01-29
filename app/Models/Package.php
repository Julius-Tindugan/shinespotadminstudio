<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\LogsActivity;

class Package extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'package_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'short_description',
        'price',
        'duration_hours',
        'max_capacity',
        'image_data',
        'image_mime_type',
        'image_name',
        'image_size',
        'description',
        'is_active',
        'sort_order',
        'is_featured',
        'max_bookings_per_day'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'image_data', // Hide binary data from JSON serialization
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'price' => 'float',
        'duration_hours' => 'decimal:2',
        'max_capacity' => 'integer',
        'sort_order' => 'integer',
        'max_bookings_per_day' => 'integer',
        'image_size' => 'integer',
    ];

    /**
     * Boot the model and set up slug generation.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->title);
            }
        });
        
        static::updating(function ($package) {
            if ($package->isDirty('title')) {
                $package->slug = Str::slug($package->title);
            }
        });
    }

    /**
     * Get the category that owns the package.
     */
    public function category()
    {
        return $this->belongsTo(PackageCategory::class, 'category_id', 'category_id');
    }



    /**
     * Get the inclusions for the package.
     */
    public function inclusions()
    {
        return $this->hasMany(PackageInclusion::class, 'package_id');
    }

    /**
     * Get the free items for the package.
     */
    public function freeItems()
    {
        return $this->hasMany(PackageFreeItem::class, 'package_id');
    }
    
    /**
     * Get the addons that can be added to this package.
     */
    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'package_addons', 'package_id', 'addon_id')
                    ->using(PackageAddon::class)
                    ->withTimestamps();
    }

    /**
     * Get the bookings that use this package.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'package_id');
    }

    /**
     * Get the image URL for display purposes.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_data) {
            // Add cache-busting parameter to prevent browser caching issues
            $timestamp = $this->updated_at ? $this->updated_at->timestamp : time();
            return route('package.image', ['id' => $this->package_id]) . '?v=' . $timestamp;
        }
        
        return null;
    }

    /**
     * Get base64 encoded image data for direct embedding.
     *
     * @return string|null
     */
    public function getImageBase64Attribute()
    {
        if ($this->image_data && $this->image_mime_type) {
            return 'data:' . $this->image_mime_type . ';base64,' . $this->image_data;
        }
        
        return null;
    }

    /**
     * Check if package has an image.
     *
     * @return bool
     */
    public function hasImage()
    {
        return !empty($this->image_data);
    }

    /**
     * Scope a query to only include active packages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured packages.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get formatted price with currency symbol.
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format((float) $this->price, 2);
    }



    /**
     * Check if we can make this package featured.
     * Maximum of 4 packages can be featured at once.
     *
     * @param int|null $excludeId Package ID to exclude from count (for updates)
     * @return bool
     */
    public static function canBeFeatured($excludeId = null)
    {
        $query = static::where('is_featured', true);
        
        if ($excludeId) {
            $query->where('package_id', '!=', $excludeId);
        }
        
        return $query->count() < 4;
    }

    /**
     * Get the count of currently featured packages.
     *
     * @param int|null $excludeId Package ID to exclude from count
     * @return int
     */
    public static function getFeaturedCount($excludeId = null)
    {
        $query = static::where('is_featured', true);
        
        if ($excludeId) {
            $query->where('package_id', '!=', $excludeId);
        }
        
        return $query->count();
    }

    /**
     * Get the maximum number of packages that can be featured.
     *
     * @return int
     */
    public static function getMaxFeaturedLimit()
    {
        return 4;
    }

    /**
     * Validate featured status change.
     *
     * @param bool $isFeatured
     * @param int|null $excludeId
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateFeaturedStatus($isFeatured, $excludeId = null)
    {
        if (!$isFeatured) {
            return ['valid' => true, 'message' => ''];
        }

        $currentCount = static::getFeaturedCount($excludeId);
        $maxLimit = static::getMaxFeaturedLimit();

        if ($currentCount >= $maxLimit) {
            return [
                'valid' => false, 
                'message' => "Cannot feature this package. Maximum of {$maxLimit} packages can be featured at once. Currently {$currentCount} packages are featured."
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Get formatted duration string (e.g., "2h 30m", "1h", "45m")
     *
     * @return string|null
     */
    public function getFormattedDurationAttribute()
    {
        // Check if duration_hours is null or 0
        if ($this->duration_hours === null || $this->duration_hours == 0) {
            return null;
        }

        // Convert decimal hours to total minutes and round to avoid floating point issues
        $totalMinutes = round($this->duration_hours * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }
        // Always show minutes, even if 0
        $parts[] = $minutes . 'm';

        return implode(' ', $parts);
    }

    /**
     * Get duration in minutes
     *
     * @return int|null
     */
    public function getDurationInMinutesAttribute()
    {
        return $this->duration_hours ? $this->duration_hours * 60 : null;
    }

    /**
     * Set duration from hours and minutes
     *
     * @param int $hours
     * @param int $minutes
     * @return void
     */
    public function setDurationFromHoursMinutes($hours, $minutes)
    {
        $totalMinutes = ($hours * 60) + $minutes;
        // Store as decimal hours (e.g., 2.5 for 2h 30m)
        $this->duration_hours = $totalMinutes > 0 ? round($totalMinutes / 60, 2) : null;
    }

    /**
     * Get duration hours component
     *
     * @return int
     */
    public function getDurationHoursComponentAttribute()
    {
        if (!$this->duration_hours) {
            return 0;
        }
        return floor($this->duration_hours);
    }

    /**
     * Get duration minutes component
     *
     * @return int
     */
    public function getDurationMinutesComponentAttribute()
    {
        if (!$this->duration_hours) {
            return 0;
        }
        $totalMinutes = $this->duration_hours * 60;
        return round($totalMinutes % 60);
    }
}
