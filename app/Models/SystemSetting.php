<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class SystemSetting extends Model
{
    use HasFactory, LogsActivity;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_settings';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'setting_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_public',
        'is_editable',
        'category',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_public' => 'boolean',
        'is_editable' => 'boolean',
    ];
    
    /**
     * Get settings by category.
     *
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByCategory($category)
    {
        return self::where('category', $category)->get();
    }
    
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return self::castValue($setting->setting_value, $setting->setting_type);
    }
    
    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @param string $category
     * @param string $description
     * @return \App\Models\SystemSetting
     */
    public static function setValue($key, $value, $type = 'string', $category = 'general', $description = null)
    {
        $setting = self::updateOrCreate(
            ['setting_key' => $key],
            [
                'setting_value' => self::formatValue($value, $type),
                'setting_type' => $type,
                'category' => $category,
                'description' => $description,
            ]
        );
        
        return $setting;
    }
    
    /**
     * Cast a setting value to its proper type.
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
    
    /**
     * Format a value for storage.
     *
     * @param mixed $value
     * @param string $type
     * @return string
     */
    protected static function formatValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'json':
                return json_encode($value);
            default:
                return (string) $value;
        }
    }
    
    /**
     * Check if payment integration is enabled.
     *
     * @return bool
     */
    public static function isPaymentIntegrationEnabled()
    {
        return self::getValue('payment_integration_enabled', false);
    }
    
    /**
     * Get all public settings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPublicSettings()
    {
        return self::where('is_public', true)->get();
    }
}
