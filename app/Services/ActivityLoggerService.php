<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLoggerService
{
    /**
     * Log an activity.
     *
     * @param string $action
     * @param Model|null $model
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param string|null $description
     * @param Request|null $request
     * @return ActivityLog
     */
    public function log(
        string $action,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?Request $request = null
    ): ActivityLog {
        $request = $request ?: request();
        
        // Determine user type and ID
        $userType = $this->getUserType();
        $userId = $this->getUserId();

        $data = [
            'user_type' => $userType,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues ? $this->sanitizeValues($oldValues) : null,
            'new_values' => $newValues ? $this->sanitizeValues($newValues) : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
        ];

        if ($model) {
            $data['model_type'] = get_class($model);
            $data['model_id'] = $model->getKey();
        }

        return ActivityLog::create($data);
    }

    /**
     * Log a model creation.
     */
    public function logCreated(Model $model, ?string $description = null): ActivityLog
    {
        $newValues = $model->getAttributes();
        
        return $this->log(
            'created',
            $model,
            null,
            $newValues,
            $description ?: "Created new " . class_basename($model)
        );
    }

    /**
     * Log a model update.
     */
    public function logUpdated(Model $model, array $oldValues, ?string $description = null): ActivityLog
    {
        $newValues = $model->getAttributes();
        
        // Only log actual changes
        $changes = [];
        foreach ($oldValues as $key => $oldValue) {
            if (isset($newValues[$key]) && $newValues[$key] != $oldValue) {
                $changes[$key] = $newValues[$key];
            }
        }

        if (empty($changes)) {
            return null; // No actual changes to log
        }

        return $this->log(
            'updated',
            $model,
            $oldValues,
            $changes,
            $description ?: "Updated " . class_basename($model)
        );
    }

    /**
     * Log a model deletion.
     */
    public function logDeleted(Model $model, ?string $description = null): ActivityLog
    {
        $oldValues = $model->getAttributes();
        
        return $this->log(
            'deleted',
            $model,
            $oldValues,
            null,
            $description ?: "Deleted " . class_basename($model)
        );
    }

    /**
     * Log authentication events.
     */
    public function logLogin(string $userType, $userId, ?string $description = null): ActivityLog
    {
        return ActivityLog::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'action' => 'login',
            'description' => $description ?: 'User logged in',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    /**
     * Log failed login attempts.
     */
    public function logFailedLogin(string $email, ?string $description = null): ActivityLog
    {
        return ActivityLog::create([
            'user_type' => 'unknown',
            'user_id' => 0,
            'action' => 'failed_login',
            'description' => $description ?: "Failed login attempt for email: {$email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    /**
     * Log logout events.
     */
    public function logLogout(?string $description = null): ActivityLog
    {
        $userType = $this->getUserType();
        $userId = $this->getUserId();

        // If no auth user, try to get from session (for logout before session cleared)
        if (!$userType || !$userId) {
            $userType = session('user_type');
            if ($userType === 'admin') {
                $userId = session('admin_id');
            } elseif ($userType === 'staff') {
                $userId = session('staff_id');
            }
        }

        return ActivityLog::create([
            'user_type' => $userType ?? 'unknown',
            'user_id' => $userId ?? 0,
            'action' => 'logout',
            'description' => $description ?: 'User logged out',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
        ]);
    }

    /**
     * Log custom actions.
     */
    public function logAction(string $action, ?string $description = null, ?array $data = null): ActivityLog
    {
        return $this->log(
            $action,
            null,
            null,
            $data,
            $description
        );
    }

    /**
     * Get the current user type.
     */
    private function getUserType(): ?string
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
     */
    private function getUserId(): ?int
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
     */
    private function sanitizeValues(array $values): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($values[$field])) {
                $values[$field] = '[HIDDEN]';
            }
        }

        return $values;
    }

    /**
     * Get recent activity logs.
     */
    public function getRecentActivity(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return ActivityLog::with([])
            ->recent($limit)
            ->get();
    }

    /**
     * Get activity logs with filtering.
     */
    public function getFilteredActivity(array $filters = [], int $perPage = 15)
    {
        $query = ActivityLog::query();

        if (isset($filters['user_type']) && $filters['user_type']) {
            $query->byUserType($filters['user_type']);
        }

        if (isset($filters['action']) && $filters['action']) {
            $query->byAction($filters['action']);
        }

        if (isset($filters['model_type']) && $filters['model_type']) {
            $query->byModelType($filters['model_type']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->byDateRange($filters['start_date'], $filters['end_date']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('action', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
}