<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Staff;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Cache;
use App\Traits\LogsActivity;

class Booking extends Model
{
    use LogsActivity;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bookings';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'booking_id';
    
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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'booking_id';
    }
    
    /**
     * The "booted" method of the model.
     * Clear KPI cache when payment status changes
     */
    protected static function booted()
    {
        parent::booted();
        
        static::updated(function ($booking) {
            // Clear cache if payment_status was changed
            if ($booking->isDirty('payment_status')) {
                Cache::forget('dashboard_kpis');
                Cache::forget('revenue_statistics');
                Cache::forget('profit_statistics');
                \Log::info('KPI cache cleared due to payment_status change for booking: ' . $booking->booking_id);
            }
        });
        
        static::created(function ($booking) {
            // Clear cache when new booking is created
            Cache::forget('dashboard_kpis');
            Cache::forget('revenue_statistics');
        });
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_reference',
        'package_id',
        'client_first_name',
        'client_last_name',
        'client_email',
        'client_phone',
        'primary_staff_id',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'total_amount',
        'payment_status',
        // 'addons' - removed: use addons() relationship via booking_addons pivot table instead
        'notes',
        'backdrop_id',
        'backdrop_selections',
        'created_by',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'total_amount' => 'decimal:2',
        // Note: 'addons' column cast removed to prevent conflict with addons() relationship
        // The addons() relationship via booking_addons pivot table should be used instead
        'backdrop_selections' => 'array',
    ];
    
    /**
     * Client relationship has been removed
     */
    
    /**
     * Get the primary staff member assigned to the booking.
     */
    public function primaryStaff()
    {
        return $this->belongsTo(Staff::class, 'primary_staff_id', 'staff_id');
    }
    
    /**
     * Get the package associated with the booking.
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id', 'package_id');
    }
    
    /**
     * Get the backdrop associated with the booking.
     */
    public function backdrop()
    {
        return $this->belongsTo(Backdrop::class, 'backdrop_id', 'backdrop_id');
    }
    
    /**
     * Get the booking slot associated with the booking.
     */
    public function bookingSlot()
    {
        return $this->belongsTo(BookingSlot::class, 'booking_slot_id', 'slot_id');
    }
    
    /**
     * Get formatted backdrop selections with full details (name and color).
     * Handles both array of IDs and array of objects.
     *
     * @return array
     */
    public function getFormattedBackdropSelectionsAttribute()
    {
        $selections = $this->backdrop_selections;
        
        if (empty($selections) || !is_array($selections)) {
            return [];
        }
        
        // Check if selections already have color and name
        $firstElement = reset($selections);
        if (is_array($firstElement) && (isset($firstElement['color']) || isset($firstElement['name']))) {
            return $selections;
        }
        
        // Extract backdrop IDs from various possible formats
        $backdropIds = [];
        
        foreach ($selections as $key => $value) {
            // Handle associative array like {"1": "5", "2": "10"}
            if (is_numeric($value)) {
                $backdropIds[] = (int)$value;
            }
            // Handle array of IDs [5, 10, 15]
            elseif (is_numeric($key) && is_numeric($value)) {
                $backdropIds[] = (int)$value;
            }
            // Handle array of objects with id [{"id": 5}, {"backdrop_id": 10}]
            elseif (is_array($value)) {
                if (isset($value['id'])) {
                    $backdropIds[] = (int)$value['id'];
                } elseif (isset($value['backdrop_id'])) {
                    $backdropIds[] = (int)$value['backdrop_id'];
                }
            }
        }
        
        if (empty($backdropIds)) {
            return [];
        }
        
        // Remove duplicates
        $backdropIds = array_unique($backdropIds);
        
        // Fetch backdrop details from database
        $backdrops = Backdrop::whereIn('backdrop_id', $backdropIds)
            ->select('backdrop_id', 'name', 'color_code')
            ->get()
            ->keyBy('backdrop_id');
        
        // Format the selections with backdrop details maintaining order
        $formatted = [];
        foreach ($backdropIds as $id) {
            if ($backdrops->has($id)) {
                $backdrop = $backdrops->get($id);
                $formatted[] = [
                    'id' => $backdrop->backdrop_id,
                    'name' => $backdrop->name,
                    'color' => $backdrop->color_code
                ];
            }
        }
        
        return $formatted;
    }
    
    /**
     * Get all staff members assigned to this booking.
     * Returns the primary staff.
     */
    public function assignedStaff()
    {
        return Staff::where('staff_id', $this->primary_staff_id)->get();
    }
    
    /**
     * Get all payments for the booking.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }
    
    /**
     * Get all payment transactions for the booking.
     */
    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class, 'booking_id', 'booking_id');
    }
    
    /**
     * Check if this booking has time conflicts with other bookings
     *
     * @return bool
     */
    public function hasTimeConflicts()
    {
        $conflictService = app(\App\Services\BookingConflictService::class);
        
        $date = $this->booking_date instanceof \Carbon\Carbon 
            ? $this->booking_date->format('Y-m-d') 
            : \Carbon\Carbon::parse($this->booking_date)->format('Y-m-d');
        
        $startTime = \Carbon\Carbon::parse($this->start_time)->format('H:i:00');
        $endTime = \Carbon\Carbon::parse($this->end_time)->format('H:i:00');
        
        $conflictCheck = $conflictService->checkTimeSlotConflicts(
            $date,
            $startTime,
            $endTime,
            $this->primary_staff_id,
            $this->booking_id
        );
        
        return $conflictCheck['hasConflict'];
    }
    
    /**
     * Get all conflicts for this booking
     *
     * @return array
     */
    public function getConflicts()
    {
        $conflictService = app(\App\Services\BookingConflictService::class);
        
        $date = $this->booking_date instanceof \Carbon\Carbon 
            ? $this->booking_date->format('Y-m-d') 
            : \Carbon\Carbon::parse($this->booking_date)->format('Y-m-d');
        
        $startTime = \Carbon\Carbon::parse($this->start_time)->format('H:i:00');
        $endTime = \Carbon\Carbon::parse($this->end_time)->format('H:i:00');
        
        return $conflictService->checkTimeSlotConflicts(
            $date,
            $startTime,
            $endTime,
            $this->primary_staff_id,
            $this->booking_id
        );
    }
    
    /**
     * Get overlapping bookings
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOverlappingBookings()
    {
        return self::where('booking_date', $this->booking_date)
            ->where('booking_id', '!=', $this->booking_id)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->whereTime('start_time', '<', $this->end_time)
                         ->whereTime('end_time', '>', $this->start_time);
                });
            })
            ->get();
    }
    
    /**
     * Scope a query to only include bookings on a specific date
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('booking_date', $date);
    }
    
    /**
     * Scope a query to only include active bookings (not cancelled or no_show)
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'no_show']);
    }
    
    /**
     * Scope a query to find bookings that overlap with a time range
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startTime
     * @param  string  $endTime
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverlappingTimeRange($query, $startTime, $endTime)
    {
        return $query->where(function ($q) use ($startTime, $endTime) {
            $q->whereTime('start_time', '<', $endTime)
              ->whereTime('end_time', '>', $startTime);
        });
    }
    
    /**
     * Note: Main scopeUpcoming implementation is defined elsewhere in this class.
     */
    
    /**
     * The addons that belong to the booking.
     * Note: Named 'bookingAddons' to avoid conflict with 'addons' column
     */
    public function bookingAddons()
    {
        return $this->belongsToMany(Addon::class, 'booking_addons', 'booking_id', 'addon_id')
                    ->withPivot('quantity', 'price');
    }
    
    /**
     * Alias relationship method for backward compatibility
     * This allows using $booking->addons() or loading with ->load('addons')
     */
    public function addons()
    {
        return $this->bookingAddons();
    }
    
    /**
     * The equipment that belong to the booking.
     */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'booking_equipment', 'booking_id', 'equipment_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
    
    /**
     * Scope a query to only include upcoming bookings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->format('Y-m-d'))
                     ->whereNotIn('status', ['cancelled', 'completed'])
                     ->orderBy('booking_date')
                     ->orderBy('start_time');
    }
    
    /**
     * Scope a query to only include bookings for the current month.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereYear('booking_date', now()->year)
                     ->whereMonth('booking_date', now()->month);
    }
    
    /**
     * Scope a query to only include bookings for the previous month.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePreviousMonth($query)
    {
        return $query->whereYear('booking_date', now()->subMonth()->year)
                     ->whereMonth('booking_date', now()->subMonth()->month);
    }
    
    /**
     * Scope a query to only include upcoming week's bookings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentWeek($query)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        return $query->whereBetween('booking_date', [$startOfWeek, $endOfWeek]);
    }
    
    /**
     * Get formatted date and time
     *
     * @return string
     */
    public function getFormattedDateTimeAttribute()
    {
        if (!$this->booking_date || !$this->start_time) {
            return 'Not scheduled';
        }
        
        return $this->booking_date->format('M d, Y') . ' at ' . 
               \Carbon\Carbon::parse($this->start_time)->format('h:i A');
    }
    
    /**
     * Get client full name
     *
     * @return string
     */
    public function getClientNameAttribute()
    {
        return $this->client_first_name . ' ' . $this->client_last_name;
    }
    
    /**
     * Get booking duration in hours and minutes
     *
     * @return string
     */
    public function getDurationAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return 'Unknown';
        }
        
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        $diffInMinutes = $start->diffInMinutes($end);
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;
        
        return ($hours > 0 ? $hours . ' hr' . ($hours > 1 ? 's' : '') . ' ' : '') . 
               ($minutes > 0 ? $minutes . ' min' : '');
    }

    /**
     * Get selected addons safely handling both relationship and array data
     *
     * @return array
     */
    public function getSelectedAddonsAttribute()
    {
        $selectedAddons = [];
        
        // Check if we have relationship data (Collection) or array data
        if ($this->relationLoaded('addons')) {
            $addonsRelation = $this->getRelation('addons');
            if ($addonsRelation instanceof \Illuminate\Database\Eloquent\Collection && $addonsRelation->count() > 0) {
                $selectedAddons = $addonsRelation->pluck('pivot.quantity', 'addon_id')->toArray();
                return $selectedAddons; // Return early if we have relationship data
            }
        }
        
        // Using the cast array attribute (legacy data in addons column)
        $addonsArray = $this->getAttributeValue('addons');
        if (is_array($addonsArray)) {
            foreach ($addonsArray as $addon) {
                if (isset($addon['id']) && isset($addon['quantity'])) {
                    $selectedAddons[$addon['id']] = $addon['quantity'];
                }
            }
        }
        
        return $selectedAddons;
    }

    /**
     * Check if booking has addons (either relationship or array data)
     *
     * @return bool
     */
    public function hasAddons()
    {
        // Check relationship first - access directly to avoid conflict with attribute
        if ($this->relationLoaded('addons')) {
            $addonsRelation = $this->getRelation('addons');
            if ($addonsRelation instanceof \Illuminate\Database\Eloquent\Collection && $addonsRelation->count() > 0) {
                return true;
            }
        }
        
        // If no relationship data, check array data from attribute cast
        $addonsAttribute = $this->getAttributeValue('addons');
        if (is_array($addonsAttribute) && !empty($addonsAttribute)) {
            return true;
        }
        
        return false;
    }
}
