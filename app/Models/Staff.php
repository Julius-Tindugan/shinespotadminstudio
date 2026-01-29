<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\LogsActivity;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, LogsActivity;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff_users';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'staff_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'password_hash',
        'status',
        'admin_id',
        'last_login',
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
        'last_login' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'password_changed_at' => 'datetime',
        'force_password_change' => 'boolean',
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
     * Get the admin that supervises this staff member.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
    
    /**
     * The roles that belong to the staff member.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'staff_roles', 'staff_id', 'role_id');
    }
    
    /**
     * Check if the staff member has a specific role.
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('role_name', $roleName)->exists();
    }
    
    /**
     * Get the full name of the staff member.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    
    /**
     * Check if the staff account is locked.
     */
    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }
    
    /**
     * Lock the staff account for a specified duration.
     */
    public function lockAccount($minutes = 30)
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }
    
    /**
     * Unlock the staff account.
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
     * Check if staff can access financial data (they should not be able to).
     */
    public function canAccessFinance()
    {
        return false; // Staff should never access financial data
    }
}
