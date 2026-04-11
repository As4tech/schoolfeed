<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolSetting;
use Illuminate\Database\Seeder;

class SchoolSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();
        $defaultSettings = config('school_settings.defaults');
        
        foreach ($schools as $school) {
            foreach ($defaultSettings as $category => $settings) {
                foreach ($settings as $key => $config) {
                    SchoolSetting::updateOrCreate(
                        [
                            'school_id' => $school->id,
                            'key' => $key,
                        ],
                        [
                            'value' => is_array($config['value']) ? json_encode($config['value']) : $config['value'],
                            'type' => $config['type'],
                            'category' => $category,
                        ]
                    );
                }
            }
        }
    }
}
