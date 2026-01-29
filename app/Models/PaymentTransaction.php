<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\LogsActivity;

class PaymentTransaction extends Model
{
    use LogsActivity;
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
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // Clear KPI cache whenever a payment transaction is created or updated
        static::created(function () {
            \Illuminate\Support\Facades\Cache::forget('dashboard_kpis');
            \Illuminate\Support\Facades\Cache::forget('revenue_statistics');
        });
        
        static::updated(function () {
            \Illuminate\Support\Facades\Cache::forget('dashboard_kpis');
            \Illuminate\Support\Facades\Cache::forget('revenue_statistics');
        });
    }
    
    /**
     * Get the booking that owns the payment transaction.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
    
    /**
     * Get the user who processed the payment.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    
    /**
     * Scope a query to only include successful transactions.
     */
    public function scopeSuccessful($query)
    {
        return $query->where(function($q) {
            $q->whereIn('xendit_status', ['PAID', 'SETTLED', 'SUCCEEDED', 'COMPLETED', 'SUCCESS'])
              ->orWhereIn('payment_method', ['onsite_cash', 'onsite_card']); // Onsite always successful
        });
    }
    
    /**
     * Scope a query to only include pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('payment_method', 'gcash')
                     ->whereIn('xendit_status', ['PENDING', 'PENDING_PAYMENT']);
    }
    
    /**
     * Scope a query to only include failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('payment_method', 'gcash')
                     ->whereIn('xendit_status', ['FAILED', 'EXPIRED']);
    }
    
    /**
     * Check if the transaction is successful.
     */
    public function isSuccessful()
    {
        // Onsite payments are always successful regardless of xendit_status
        if (in_array($this->payment_method, ['onsite_cash', 'onsite_card'])) {
            return true;
        }
        
        // GCash payments need successful xendit_status
        // Xendit statuses: SUCCEEDED (e-wallet), PAID/SETTLED (invoice), COMPLETED (general)
        return in_array(strtoupper($this->xendit_status ?? ''), ['PAID', 'SETTLED', 'SUCCEEDED', 'COMPLETED', 'SUCCESS']);
    }
    
    /**
     * Check if the transaction is pending.
     */
    public function isPending()
    {
        return in_array($this->xendit_status, ['PENDING', 'PENDING_PAYMENT']);
    }
    
    /**
     * Check if the transaction failed.
     */
    public function isFailed()
    {
        return in_array($this->xendit_status, ['FAILED', 'EXPIRED']);
    }
    
    /**
     * Get formatted payment method name.
     */
    public function getFormattedPaymentMethodAttribute()
    {
        return match($this->payment_method) {
            'gcash' => 'GCash (Online)',
            'onsite_cash' => 'Cash (Onsite)',
            'onsite_card' => 'Card (Onsite)',
            default => ucfirst($this->payment_method)
        };
    }
    
    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format($this->amount, 2);
    }
    
    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        if ($this->isSuccessful()) {
            return 'bg-green-100 text-green-800';
        } elseif ($this->isPending()) {
            return 'bg-yellow-100 text-yellow-800';
        } elseif ($this->isFailed()) {
            return 'bg-red-100 text-red-800';
        }
        
        return 'bg-gray-100 text-gray-800';
    }
    
    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute()
    {
        if ($this->isSuccessful()) {
            return 'Paid';
        } elseif ($this->isPending()) {
            return 'Pending';
        } elseif ($this->isFailed()) {
            return 'Failed';
        }
        
        return $this->xendit_status ?? 'Unknown';
    }
    
    /**
     * Create a payment transaction record.
     */
    public static function createTransaction($bookingId, $paymentMethod, $amount, $options = [])
    {
        return static::create([
            'booking_id' => $bookingId,
            'payment_method' => $paymentMethod,
            'amount' => $amount,
            'transaction_reference' => $options['transaction_reference'] ?? null,
            'xendit_payment_id' => $options['xendit_payment_id'] ?? null,
            'xendit_status' => $options['xendit_status'] ?? ($paymentMethod === 'gcash' ? 'PENDING' : 'PAID'),
            'payment_date' => $options['payment_date'] ?? now(),
            'processed_by' => $options['processed_by'] ?? auth()->id(),
            'notes' => $options['notes'] ?? null,
            'metadata' => $options['metadata'] ?? null,
        ]);
    }
}