<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'category_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'color_code',
        'color',
        'is_active',
        'budget_tracking',
        'budget',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'budget_tracking' => 'boolean',
        'budget' => 'float',
    ];
    
    /**
     * Get all expenses in this category.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id', 'category_id');
    }
    
    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
