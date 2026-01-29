<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Addon extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'addon_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'addon_name',
        'addon_price',
        'description',
        'is_active'
    ];

    /**
     * The packages that this addon belongs to.
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_addons', 'addon_id', 'package_id')
                    ->using(PackageAddon::class)
                    ->withTimestamps();
    }

    /**
     * The bookings that use this addon.
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_addons', 'addon_id', 'booking_id')
                    ->withPivot('quantity', 'price');
    }
}
