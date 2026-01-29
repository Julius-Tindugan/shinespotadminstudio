<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingLog extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the booking that owns the log.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the user who created the log.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Log a booking action
     *
     * @param Booking $booking
     * @param string $action
     * @param array $oldValues
     * @param array $newValues
     * @param string $ipAddress
     * @param string $userAgent
     * @param int $createdBy
     * @return static
     */
    public static function log($booking, $action, $oldValues = null, $newValues = null, $ipAddress = null, $userAgent = null, $createdBy = null)
    {
        return static::create([
            'booking_id' => $booking->booking_id,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $ipAddress ?: request()->ip(),
            'user_agent' => $userAgent ?: request()->userAgent(),
            'created_by' => $createdBy,
        ]);
    }
}
