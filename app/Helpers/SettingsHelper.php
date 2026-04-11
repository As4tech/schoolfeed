<?php

namespace App\Helpers;

use App\Models\SchoolSetting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Get a setting value for a specific school
     * 
     * @param int|string $schoolId
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getSetting($schoolId, $key, $default = null)
    {
        // Try to get from database first
        $value = SchoolSetting::getValue($schoolId, $key);
        
        // If not found, get from config defaults
        if ($value === null) {
            $configDefaults = config('school_settings.defaults');
            
            foreach ($configDefaults as $category => $settings) {
                if (isset($settings[$key])) {
                    $defaultConfig = $settings[$key];
                    return $defaultConfig['value'] ?? $default;
                }
            }
            
            return $default;
        }
        
        return $value;
    }
    
    /**
     * Get all settings for a school, merging with defaults
     * 
     * @param int|string $schoolId
     * @return array
     */
    public static function getAllSettings($schoolId)
    {
        $dbSettings = SchoolSetting::getAllForSchool($schoolId);
        $configDefaults = config('school_settings.defaults');
        $allSettings = [];
        
        // Merge config defaults
        foreach ($configDefaults as $category => $settings) {
            foreach ($settings as $key => $config) {
                $allSettings[$key] = $config['value'];
            }
        }
        
        // Override with database values
        foreach ($dbSettings as $key => $value) {
            $allSettings[$key] = $value;
        }
        
        return $allSettings;
    }
    
    /**
     * Get settings by category for a school
     * 
     * @param int|string $schoolId
     * @param string $category
     * @return array
     */
    public static function getSettingsByCategory($schoolId, $category)
    {
        $allSettings = self::getAllSettings($schoolId);
        $configDefaults = config("school_settings.defaults.{$category}", []);
        $categorySettings = [];
        
        foreach ($configDefaults as $key => $config) {
            $categorySettings[$key] = [
                'value' => $allSettings[$key] ?? $config['value'],
                'type' => $config['type'],
                'label' => $config['label'],
                'description' => $config['description'],
                'options' => $config['options'] ?? null,
            ];
        }
        
        return $categorySettings;
    }
    
    /**
     * Set a setting value for a specific school
     * 
     * @param int|string $schoolId
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function setSetting($schoolId, $key, $value)
    {
        $configDefaults = config('school_settings.defaults');
        $type = 'string';
        $category = 'general';
        
        // Find the config to get type and category
        foreach ($configDefaults as $cat => $settings) {
            if (isset($settings[$key])) {
                $type = $settings[$key]['type'];
                $category = $cat;
                break;
            }
        }
        
        SchoolSetting::setValue($schoolId, $key, $value, $type, $category);
        
        return true;
    }
    
    /**
     * Get the current school ID from context
     * 
     * @return int|null
     */
    public static function getCurrentSchoolId()
    {
        // Try to get from request route
        if (request()->route('school')) {
            $school = request()->route('school');
            if (is_object($school)) {
                return $school->id;
            }
            // If it's a slug, find the school
            $schoolModel = \App\Models\School::where('slug', $school)->first();
            return $schoolModel ? $schoolModel->id : null;
        }
        
        // Try to get from authenticated user
        if (auth()->check() && auth()->user()->school_id) {
            return auth()->user()->school_id;
        }
        
        return null;
    }
    
    /**
     * Get setting for the current school
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getCurrentSchoolSetting($key, $default = null)
    {
        $schoolId = self::getCurrentSchoolId();
        
        if (!$schoolId) {
            return $default;
        }
        
        return self::getSetting($schoolId, $key, $default);
    }
}
