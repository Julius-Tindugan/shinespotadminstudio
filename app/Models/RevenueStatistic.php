<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RevenueStatistic extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'total_revenue',
        'total_expenses',
        'net_profit',
        'completed_bookings',
        'canceled_bookings',
        'payment_received',
        'payment_pending',
        'daily_revenue',
        'weekly_revenue',
        'monthly_revenue',
        'yearly_revenue',
        'year',
        'month',
        'week',
        'day',
        'transaction_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'completed_bookings' => 'integer',
        'canceled_bookings' => 'integer',
        'payment_received' => 'decimal:2',
        'payment_pending' => 'decimal:2',
        'daily_revenue' => 'decimal:2',
        'weekly_revenue' => 'decimal:2',
        'monthly_revenue' => 'decimal:2',
        'yearly_revenue' => 'decimal:2',
        'year' => 'integer',
        'month' => 'integer',
        'week' => 'integer',
        'day' => 'integer',
        'transaction_count' => 'integer',
    ];

    /**
     * Scope a query to only include statistics for the current day.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    /**
     * Scope a query to only include statistics for the current month.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentMonth($query)
    {
        return $query->where('year', Carbon::now()->year)
                     ->where('month', Carbon::now()->month);
    }

    /**
     * Scope a query to only include statistics for the current week.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentWeek($query)
    {
        return $query->where('year', Carbon::now()->year)
                     ->where('week', Carbon::now()->week);
    }

    /**
     * Scope a query to only include statistics for the current year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentYear($query)
    {
        return $query->where('year', Carbon::now()->year);
    }

    /**
     * Scope a query to only include statistics for a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|Carbon  $startDate
     * @param  string|Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get the formatted total revenue with currency symbol.
     *
     * @return string
     */
    public function getFormattedTotalRevenueAttribute()
    {
        return '₱' . number_format($this->total_revenue, 2);
    }

    /**
     * Get the formatted total expenses with currency symbol.
     *
     * @return string
     */
    public function getFormattedTotalExpensesAttribute()
    {
        return '₱' . number_format($this->total_expenses, 2);
    }

    /**
     * Get the formatted net profit with currency symbol.
     *
     * @return string
     */
    public function getFormattedNetProfitAttribute()
    {
        return '₱' . number_format($this->net_profit, 2);
    }
    
    /**
     * Get the formatted payment received with currency symbol.
     *
     * @return string
     */
    public function getFormattedPaymentReceivedAttribute()
    {
        return '₱' . number_format($this->payment_received, 2);
    }
    
    /**
     * Get the formatted payment pending with currency symbol.
     *
     * @return string
     */
    public function getFormattedPaymentPendingAttribute()
    {
        return '₱' . number_format($this->payment_pending, 2);
    }
}
