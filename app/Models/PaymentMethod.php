<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'method_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'method_name',
        'method_type',
        'is_active',
        'description',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Get all payments using this method.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'payment_method', 'method_id');
    }
    
    /**
     * Scope a query to only include active payment methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Scope a query to only include online payment methods.
     */
    public function scopeOnline($query)
    {
        return $query->where('method_type', 'online');
    }
    
    /**
     * Scope a query to only include onsite payment methods.
     */
    public function scopeOnsite($query)
    {
        return $query->where('method_type', 'onsite');
    }
}
