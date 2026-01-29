<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
        'is_public'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_public' => 'boolean',
    ];
    
    /**
     * Get all settings for a specific group
     *
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getGroup($group)
    {
        return static::where('group', $group)->get();
    }
    
    /**
     * Get all settings as key-value pairs for a specific group
     *
     * @param string $group
     * @return array
     */
    public static function getGroupAsArray($group)
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
    
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        return $setting ? $setting->value : $default;
    }
    
    /**
     * Set a setting value by key
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function setValue($key, $value)
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        
        return $setting->save();
    }
    
    /**
     * Update multiple settings at once
     *
     * @param array $data
     * @return bool
     */
    public static function updateSettings(array $data)
    {
        foreach ($data as $key => $value) {
            static::setValue($key, $value);
        }
        
        return true;
    }
}
