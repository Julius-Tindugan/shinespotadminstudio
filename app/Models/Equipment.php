<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class Equipment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'equipment';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'equipment_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'image',
        'quantity',
        'cost',
        'purchase_date',
        'condition',
        'is_available',
        'is_active'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'purchase_date' => 'date',
        'cost' => 'decimal:2',
        'quantity' => 'integer',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Get the bookings that use this equipment
     */
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_equipment', 'equipment_id', 'booking_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
