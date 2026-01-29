<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Backdrop extends Model
{
    use HasFactory, LogsActivity;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'backdrops';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'backdrop_id';
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
    
    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'int';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color_code',
        'description',
        'is_active'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Get all bookings that use this backdrop
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'backdrop_id');
    }
}
