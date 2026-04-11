<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'key',
        'value',
        'type',
        'category',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Scope to get general settings
     */
    public function scopeGeneral($query)
    {
        return $query->where('category', 'general');
    }

    /**
     * Scope to get payment settings
     */
    public function scopePayment($query)
    {
        return $query->where('category', 'payment');
    }

    /**
     * Scope to get feeding settings
     */
    public function scopeFeeding($query)
    {
        return $query->where('category', 'feeding');
    }

    /**
     * Scope to get notification settings
     */
    public function scopeNotification($query)
    {
        return $query->where('category', 'notification');
    }

    /**
     * Get a setting value for a specific school
     */
    public static function getValue($schoolId, $key, $default = null)
    {
        $cacheKey = "school_settings_{$schoolId}_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($schoolId, $key, $default) {
            $setting = static::where('school_id', $schoolId)
                ->where('key', $key)
                ->first();
            
            if (!$setting) {
                return $default;
            }

            return match($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                'float' => (float) $setting->value,
                'json', 'array' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }

    /**
     * Set a setting value for a specific school
     */
    public static function setValue($schoolId, $key, $value, $type = 'string', $category = 'general')
    {
        $setting = static::updateOrCreate(
            ['school_id' => $schoolId, 'key' => $key],
            [
                'value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
                'type' => $type,
                'category' => $category,
            ]
        );

        // Clear cache for this setting
        Cache::forget("school_settings_{$schoolId}_{$key}");
        
        // Clear all settings cache for this school
        Cache::forget("school_settings_all_{$schoolId}");
        
        // Try to clear tagged cache if available (Redis/Memcached)
        try {
            Cache::tags(['settings', "school_{$schoolId}"])->flush();
        } catch (\Exception $e) {
            // Tags not supported, continue
        }

        return $setting;
    }

    /**
     * Get all settings for a school as an associative array
     */
    public static function getAllForSchool($schoolId)
    {
        $cacheKey = "school_settings_all_{$schoolId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($schoolId) {
            $settings = static::where('school_id', $schoolId)->get();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = match($setting->type) {
                    'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $setting->value,
                    'float' => (float) $setting->value,
                    'json', 'array' => json_decode($setting->value, true),
                    default => $setting->value,
                };
            }
            
            return $result;
        });
    }

    /**
     * Relationship with School
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
