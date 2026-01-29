<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Boot the trait.
     */
    protected static function bootLogsActivity()
    {
        // Log model creation
        static::created(function ($model) {
            $model->logActivity('created');
        });

        // Log model updates
        static::updated(function ($model) {
            $model->logActivity('updated', $model->getOriginal());
        });

        // Log model deletion
        static::deleted(function ($model) {
            $model->logActivity('deleted', $model->getOriginal());
        });
    }

    /**
     * Log an activity for this model.
     *
     * @param string $action
     * @param array|null $oldValues
     * @return ActivityLog|null
     */
    public function logActivity(string $action, ?array $oldValues = null): ?ActivityLog
    {
        // Determine user type and ID
        $userType = $this->getCurrentUserType();
        $userId = $this->getCurrentUserId();

        // Skip logging if no authenticated user (e.g., system operations)
        if (!$userType || !$userId) {
            return null;
        }

        $data = [
            'user_type' => $userType,
            'user_id' => $userId,
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->getKey(),
            'description' => $this->generateDescription($action),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ];

        // Add old and new values
        if ($action === 'created') {
            $data['new_values'] = $this->sanitizeValues($this->getAttributes());
        } elseif ($action === 'updated') {
            $data['old_values'] = $this->sanitizeValues($oldValues);
            $data['new_values'] = $this->sanitizeValues($this->getChanges());
        } elseif ($action === 'deleted') {
            $data['old_values'] = $this->sanitizeValues($oldValues);
        }

        return ActivityLog::create($data);
    }

    /**
     * Generate a description for the activity.
     *
     * @param string $action
     * @return string
     */
    protected function generateDescription(string $action): string
    {
        $modelName = class_basename($this);
        $modelName = preg_replace('/(?<!^)([A-Z])/', ' $1', $modelName);
        $modelName = strtolower($modelName);

        $identifier = $this->getActivityIdentifier();

        switch ($action) {
            case 'created':
                return "Created new {$modelName}: {$identifier}";
            case 'updated':
                return "Updated {$modelName}: {$identifier}";
            case 'deleted':
                return "Deleted {$modelName}: {$identifier}";
            default:
                return "Performed {$action} on {$modelName}: {$identifier}";
        }
    }

    /**
     * Get a human-readable identifier for the model.
     *
     * @return string
     */
    protected function getActivityIdentifier(): string
    {
        // Check for common name fields
        $nameFields = ['booking_reference', 'name', 'title', 'email', 'first_name', 'reference_number'];
        
        foreach ($nameFields as $field) {
            if (isset($this->attributes[$field])) {
                return $this->attributes[$field];
            }
        }

        // Combine first and last name if available
        if (isset($this->attributes['first_name']) && isset($this->attributes['last_name'])) {
            return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
        }

        // Combine client names for bookings
        if (isset($this->attributes['client_first_name']) && isset($this->attributes['client_last_name'])) {
            return $this->attributes['client_first_name'] . ' ' . $this->attributes['client_last_name'];
        }

        // Fall back to ID
        return '#' . $this->getKey();
    }

    /**
     * Get the current user type.
     *
     * @return string|null
     */
    protected function getCurrentUserType(): ?string
    {
        // Check Laravel Auth guards first
        if (Auth::guard('admin')->check()) {
            return 'admin';
        } elseif (Auth::guard('staff')->check()) {
            return 'staff';
        }
        
        // Fallback to session-based authentication
        if (session('admin_logged_in') && session('admin_id')) {
            return 'admin';
        } elseif (session('staff_logged_in') && session('staff_id')) {
            return 'staff';
        }
        
        return null;
    }

    /**
     * Get the current user ID.
     *
     * @return int|null
     */
    protected function getCurrentUserId(): ?int
    {
        // Check Laravel Auth guards first
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->id();
        } elseif (Auth::guard('staff')->check()) {
            return Auth::guard('staff')->id();
        }
        
        // Fallback to session-based authentication
        if (session('admin_logged_in') && session('admin_id')) {
            return (int) session('admin_id');
        } elseif (session('staff_logged_in') && session('staff_id')) {
            return (int) session('staff_id');
        }
        
        return null;
    }

    /**
     * Sanitize values by removing sensitive information.
     *
     * @param array $values
     * @return array
     */
    protected function sanitizeValues(array $values): array
    {
        $sensitiveFields = [
            'password', 
            'password_confirmation', 
            'token', 
            'api_key', 
            'secret',
            'remember_token',
            'stripe_secret',
            'paypal_secret'
        ];
        
        foreach ($sensitiveFields as $field) {
            if (isset($values[$field])) {
                $values[$field] = '[HIDDEN]';
            }
        }

        return $values;
    }
}
