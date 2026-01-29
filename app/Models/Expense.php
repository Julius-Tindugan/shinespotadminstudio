<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Expense extends Model
{
    use LogsActivity;
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'expense_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'amount',
        'expense_date',
        'category',
        'receipt_image',
        'is_recurring',
        'recurring_interval',
        'booking_id',
        'created_by'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
    ];
    
    /**
     * Get the category that the expense belongs to.
     * 
     * Note: This relationship is now based on the category name string rather than a foreign key
     * since the database is using the older schema with a 'category' column.
     */
    public function categoryRelation()
    {
        // Using the name field from ExpenseCategory to match with the category string
        return $this->belongsTo(ExpenseCategory::class, 'category', 'name');
    }
    
    /**
     * Get the booking that the expense is related to (if any).
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
    
    /**
     * Get the admin who created the expense.
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'admin_id');
    }
    
    /**
     * Scope a query to only include expenses for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }
    
    /**
     * Scope a query to only include expenses for a specific category.
     */
    public function scopeCategory($query, $categoryName)
    {
        return $query->where('category', $categoryName);
    }
}
