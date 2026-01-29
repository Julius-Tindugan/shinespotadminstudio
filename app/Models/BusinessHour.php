<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Traits\LogsActivity;

class BusinessHour extends Model
{
    use HasFactory, LogsActivity;
    
    protected $table = 'business_hours';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_closed' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $appends = ['day_name', 'formatted_open_time', 'formatted_close_time'];

    /**
     * Get the name of the day of the week.
     *
     * @return string
     */
    public function getDayNameAttribute()
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return $days[$this->day_of_week];
    }

    /**
     * Format time for display
     * 
     * @param string $time
     * @return string
     */
    public function formatTime($time)
    {
        if (!$time) return '';
        return date('g:i A', strtotime($time));
    }

    /**
     * Get formatted open time
     */
    public function getFormattedOpenTimeAttribute()
    {
        if ($this->is_closed || !$this->open_time) {
            return null;
        }
        return $this->formatTime($this->open_time);
    }
    
    /**
     * Get formatted close time
     */
    public function getFormattedCloseTimeAttribute()
    {
        if ($this->is_closed || !$this->close_time) {
            return null;
        }
        return $this->formatTime($this->close_time);
    }
}
