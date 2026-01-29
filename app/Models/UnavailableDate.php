<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Traits\LogsActivity;

class UnavailableDate extends Model
{
    use HasFactory, LogsActivity;
    
    protected $table = 'unavailable_dates';
    protected $primaryKey = 'date_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_date',
        'end_date',
        'reason',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array
     */
    protected $appends = [
        'formatted_start_date', 
        'formatted_end_date',
        'formatted_date_range',
        'creator_name',
        'days_count'
    ];

    /**
     * Get the admin who created the unavailable date entry.
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'admin_id');
    }
    
    /**
     * Get the creator name or 'Unknown' if no creator found
     *
     * @return string
     */
    public function getCreatorNameAttribute()
    {
        return $this->creator ? $this->creator->name : 'Unknown';
    }
    
    /**
     * Calculate the number of days between start and end date
     *
     * @return int
     */
    public function getDaysCountAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }
        
        return $this->start_date->diffInDays($this->end_date) + 1; // +1 to include both start and end dates
    }
    
    /**
     * Get the formatted start date
     *
     * @return string
     */
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('M d, Y');
    }
    
    /**
     * Get the formatted end date
     *
     * @return string
     */
    public function getFormattedEndDateAttribute()
    {
        return $this->end_date->format('M d, Y');
    }
    
    /**
     * Get the date range as a formatted string
     *
     * @return string
     */
    public function getFormattedDateRangeAttribute()
    {
        if ($this->start_date->eq($this->end_date)) {
            return $this->formatted_start_date;
        }
        
        return $this->formatted_start_date . ' - ' . $this->formatted_end_date;
    }
    
    /**
     * Check if this date range overlaps with another date range
     *
     * @param string|Carbon $startDate
     * @param string|Carbon $endDate
     * @param int|null $excludeId
     * @return bool
     */
    public static function hasOverlap($startDate, $endDate, $excludeId = null)
    {
        $query = self::where(function($q) use ($startDate, $endDate) {
            // This unavailable date overlaps with the given range
            $q->where(function($q) use ($startDate, $endDate) {
                $q->where('start_date', '<=', $endDate)
                  ->where('end_date', '>=', $startDate);
            });
        });
        
        if ($excludeId) {
            $query->where('date_id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}
