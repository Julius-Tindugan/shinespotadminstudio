<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingSlot extends Model
{
    use HasFactory;
    
    protected $table = 'booking_slots';
    protected $primaryKey = 'slot_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'time_slot',
        'max_bookings',
        'current_bookings',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'max_bookings' => 'integer',
        'current_bookings' => 'integer',
    ];
    
    /**
     * The attributes that should be appended to arrays.
     *
     * @var array
     */
    protected $appends = ['formatted_time', 'formatted_date', 'formatted_datetime', 'availability_status'];

    /**
     * Check if the slot is fully booked.
     * 
     * @return bool
     */
    public function isFullyBooked()
    {
        if ($this->max_bookings === null) {
            return false;
        }
        
        return $this->current_bookings >= $this->max_bookings;
    }

    /**
     * Check if the slot has bookings.
     * 
     * @return bool
     */
    public function hasBookings()
    {
        return $this->current_bookings > 0;
    }
    
    /**
     * Get bookings for this slot
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'booking_slot_id', 'slot_id');
    }
    
    /**
     * Get the formatted time slot (12-hour format)
     *
     * @return string
     */
    public function getFormattedTimeAttribute()
    {
        return Carbon::parse($this->time_slot)->format('g:i A');
    }
    
    /**
     * Get the formatted date
     *
     * @return string
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('M d, Y');
    }
    
    /**
     * Get the formatted date and time
     *
     * @return string
     */
    public function getFormattedDatetimeAttribute()
    {
        return $this->formatted_date . ' at ' . $this->formatted_time;
    }
    
    /**
     * Get the availability status as a string
     *
     * @return string
     */
    public function getAvailabilityStatusAttribute()
    {
        if (!$this->is_available) {
            return 'Unavailable';
        }
        
        if ($this->isFullyBooked()) {
            return 'Fully Booked';
        }
        
        return 'Available';
    }
    
    /**
     * Create or update booking slots for a specific date range and business hours
     *
     * @param string|Carbon $startDate
     * @param string|Carbon $endDate
     * @param array $businessHours
     * @param int $slotDurationMinutes
     * @param bool $setAvailable
     * @return void
     */
    public static function generateSlotsForDateRange($startDate, $endDate, $businessHours, $slotDurationMinutes = 60, $setAvailable = true)
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();
        
        // Create date period
        $period = Carbon::parse($startDate)->daysUntil($endDate);
        
        foreach ($period as $date) {
            $dayOfWeek = $date->dayOfWeek;
            
            // Find the business hours for this day of the week
            $hoursForDay = collect($businessHours)->firstWhere('day_of_week', $dayOfWeek);
            
            // Skip if the business is closed on this day
            if (!$hoursForDay || $hoursForDay['is_closed']) {
                continue;
            }
            
            $openTime = Carbon::parse($date->format('Y-m-d') . ' ' . $hoursForDay['open_time']);
            $closeTime = Carbon::parse($date->format('Y-m-d') . ' ' . $hoursForDay['close_time']);
            
            // Generate time slots
            $currentTime = clone $openTime;
            while ($currentTime->lt($closeTime)) {
                $timeSlot = $currentTime->format('H:i:s');
                
                self::updateOrCreate(
                    [
                        'date' => $date->format('Y-m-d'),
                        'time_slot' => $timeSlot
                    ],
                    [
                        'is_available' => $setAvailable
                    ]
                );
                
                $currentTime->addMinutes($slotDurationMinutes);
            }
        }
    }
}
