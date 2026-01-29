<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_type',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action (polymorphic relationship).
     */
    public function user()
    {
        if ($this->user_type === 'admin') {
            return $this->belongsTo(Admin::class, 'user_id', 'admin_id');
        } elseif ($this->user_type === 'staff') {
            return $this->belongsTo(Staff::class, 'user_id', 'staff_id');
        }
        
        return null;
    }

    /**
     * Get the model that was affected (polymorphic relationship).
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Get the user's full name.
     */
    public function getUserNameAttribute()
    {
        $user = $this->user();
        if ($user) {
            $userData = $user->first();
            if ($userData) {
                return $userData->first_name . ' ' . $userData->last_name;
            }
        }
        return 'Unknown User';
    }

    /**
     * Get a human-readable action description.
     */
    public function getActionDescriptionAttribute()
    {
        $userName = $this->user_name;
        $modelName = $this->getModelDisplayName();
        
        switch ($this->action) {
            case 'created':
                return "{$userName} created a new {$modelName}";
            case 'updated':
                return "{$userName} updated {$modelName}";
            case 'deleted':
                return "{$userName} deleted {$modelName}";
            case 'login':
                return "{$userName} logged in";
            case 'logout':
                return "{$userName} logged out";
            case 'failed_login':
                return "Failed login attempt for {$userName}";
            default:
                return $this->description ?: "{$userName} performed {$this->action} on {$modelName}";
        }
    }

    /**
     * Get a display-friendly model name.
     */
    private function getModelDisplayName()
    {
        if (!$this->model_type) {
            return 'system';
        }

        $modelName = class_basename($this->model_type);
        
        // Convert camelCase to readable format
        $modelName = preg_replace('/(?<!^)([A-Z])/', ' $1', $modelName);
        
        return strtolower($modelName);
    }

    /**
     * Scope to filter by user type.
     */
    public function scopeByUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope to filter by action.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent activities.
     */
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Get a formatted summary of changes made.
     */
    public function getChangesSummaryAttribute()
    {
        if ($this->action !== 'updated' || !$this->old_values || !$this->new_values) {
            return null;
        }

        $changes = [];
        foreach ($this->new_values as $field => $newValue) {
            if (isset($this->old_values[$field])) {
                $oldValue = $this->old_values[$field];
                if ($oldValue != $newValue) {
                    $changes[] = [
                        'field' => $this->formatFieldName($field),
                        'old' => $this->formatValue($oldValue),
                        'new' => $this->formatValue($newValue),
                    ];
                }
            }
        }

        return $changes;
    }

    /**
     * Format field names to be more readable.
     */
    private function formatFieldName($field)
    {
        // Convert snake_case to Title Case
        return ucwords(str_replace('_', ' ', $field));
    }

    /**
     * Format values for display.
     */
    private function formatValue($value)
    {
        if (is_null($value)) {
            return '(empty)';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        // Truncate long strings
        if (strlen($value) > 100) {
            return substr($value, 0, 100) . '...';
        }

        return $value;
    }

    /**
     * Scope to filter by model type.
     */
    public function scopeByModelType($query, $modelType)
    {
        return $query->where('model_type', $modelType);
    }
}
