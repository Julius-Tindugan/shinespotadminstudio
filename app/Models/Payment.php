<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Payment extends Model
{
    use LogsActivity;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_transactions';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'transaction_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_id',
        'payment_method',
        'amount',
        'transaction_reference',
        'xendit_payment_id',
        'xendit_status',
        'payment_date',
        'processed_by',
        'notes',
        'metadata',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];
    
    /**
     * Get the booking that owns the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
    
    /**
     * Get the admin user who processed this payment.
     */
    public function processor()
    {
        return $this->belongsTo(AdminUser::class, 'processed_by', 'admin_id');
    }
    
    /**
     * Scope a query to only include payments for the current month.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereYear('payment_date', now()->year)
                     ->whereMonth('payment_date', now()->month);
    }
    
    /**
     * Scope a query to only include payments for the previous month.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePreviousMonth($query)
    {
        return $query->whereYear('payment_date', now()->subMonth()->year)
                     ->whereMonth('payment_date', now()->subMonth()->month);
    }
    
    /**
     * Scope a query to only include payments for the current day.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', now()->toDateString());
    }
    
    /**
     * Scope a query to only include payments for the current week.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentWeek($query)
    {
        return $query->whereBetween('payment_date', [
            now()->startOfWeek(), 
            now()->endOfWeek()
        ]);
    }
    
    /**
     * Scope a query to only include payments for the current year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereYear('payment_date', now()->year);
    }
    
    /**
     * Scope a query to only include payments for a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \DateTime|string  $startDate
     * @param  \DateTime|string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }
    
    /**
     * Scope a query to only include successful/completed payments.
     * A payment is considered completed if:
     * - It's an online payment (gcash) with xendit_status = 'SUCCEEDED', 'PAID', or 'SETTLED', OR
     * - It's an onsite payment (cash/card) - automatically considered completed, OR
     * - The linked booking has payment_status = 'paid'
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where(function($q) {
            $q->where(function($subQ) {
                // Online payments: gcash with SUCCEEDED, PAID, or SETTLED status
                $subQ->where('payment_method', 'gcash')
                     ->whereIn('xendit_status', ['SUCCEEDED', 'PAID', 'SETTLED']);
            })
            ->orWhereIn('payment_method', ['onsite_cash', 'onsite_card'])
            ->orWhereHas('booking', function($bookingQuery) {
                $bookingQuery->where('payment_status', 'paid');
            });
        });
    }
    
    /**
     * Scope a query to only include successful payments from Xendit.
     * @deprecated Use scopeCompleted instead
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('xendit_status', ['SUCCEEDED', 'PAID', 'SETTLED'])
                     ->orWhereIn('payment_method', ['onsite_cash', 'onsite_card']);
    }
    
    /**
     * Get the formatted amount with currency symbol.
     *
     * @return string
     */
    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format($this->amount, 2);
    }
    
    /**
     * Get the payment status based on payment method and xendit status.
     * Returns 'completed', 'pending', or 'failed'
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        // For GCash payments, check Xendit status
        if ($this->payment_method === 'gcash') {
            if (in_array($this->xendit_status, ['SUCCEEDED', 'PAID', 'SETTLED'])) {
                return 'completed';
            } elseif (in_array($this->xendit_status, ['PENDING', 'PENDING_PAYMENT'])) {
                return 'pending';
            } else {
                return 'failed';
            }
        }
        
        // For onsite payments, they are automatically completed
        if (in_array($this->payment_method, ['onsite_cash', 'onsite_card'])) {
            return 'completed';
        }
        
        // Check booking payment status as fallback
        if ($this->booking && $this->booking->payment_status === 'paid') {
            return 'completed';
        }
        
        return 'pending';
    }
}
