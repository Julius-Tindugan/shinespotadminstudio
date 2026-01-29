<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogsActivity;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_users';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'admin_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
        'force_password_change',
        'password_changed_at',
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password_hash',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'password_changed_at' => 'datetime',
        'force_password_change' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
    
    /**
     * Set the user's password.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password_hash'] = bcrypt($value);
    }
    
    /**
     * The roles that belong to the admin.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_roles', 'admin_id', 'role_id');
    }
    
    /**
     * Check if the admin has a specific role.
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('roles.role_name', $roleName)->exists();
    }
    
    /**
     * Check if the admin has a specific role by ID.
     */
    public function hasRoleId($roleId)
    {
        return $this->roles()->where('roles.role_id', $roleId)->exists();
    }
    
    /**
     * Check if the admin account is locked.
     */
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }
    
    /**
     * Lock the admin account for a specified duration.
     */
    public function lockAccount($minutes = 30)
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }
    
    /**
     * Unlock the admin account.
     */
    public function unlockAccount()
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }
    
    /**
     * Increment failed login attempts.
     */
    public function incrementFailedLoginAttempts()
    {
        $this->increment('failed_login_attempts');
        
        // Lock account after 5 failed attempts
        if ($this->failed_login_attempts >= 5) {
            $this->lockAccount();
        }
    }
    
    /**
     * Reset failed login attempts.
     */
    public function resetFailedLoginAttempts()
    {
        $this->update(['failed_login_attempts' => 0]);
    }
    
    /**
     * Check if the admin is a super admin.
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Check if the admin is an admin.
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
    
    /**
     * Check if the admin is staff.
     */
    public function isStaff()
    {
        return $this->hasRole('staff');
    }
    
    /**
     * Check if the admin can access financial data.
     */
    public function canAccessFinance()
    {
        // Only staff can access finance, admin has access to everything including finance
        return $this->hasRole('staff') || $this->hasRole('admin');
    }
    
    /**
     * Check if the admin can access all system features.
     */
    public function canAccessAllFeatures()
    {
        // Only admin role has access to all features
        return $this->hasRole('admin');
    }
    
    /**
     * Check if the admin can access settings.
     */
    public function canAccessSettings()
    {
        // Only admin can access settings
        return $this->hasRole('admin');
    }
    
    /**
     * Check if the admin can access bookings.
     */
    public function canAccessBookings()
    {
        // Only admin can access bookings
        return $this->hasRole('admin');
    }
    
    /**
     * Check if the admin can access packages.
     */
    public function canAccessPackages()
    {
        // Only admin can access packages
        return $this->hasRole('admin');
    }
    

    
    /**
     * Check if the admin can access studio management.
     */
    public function canAccessStudio()
    {
        // Only admin can access studio management
        return $this->hasRole('admin');
    }
    
    /**
     * Get the admin's full name.
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
