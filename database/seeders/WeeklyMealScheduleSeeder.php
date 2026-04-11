<?php

namespace Database\Seeders;

use App\Models\Meal;
use App\Models\School;
use App\Models\WeeklyMealSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WeeklyMealScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first school
        $school = School::first();
        
        if (!$school) {
            $this->command->error('No school found. Please run SchoolSeeder first.');
            return;
        }

        // Get or create meals for the schedule
        $meals = [
            'Waakye & Egg' => 10.00,
            'Banku & Fish' => 10.00,
            'Jollof Rice & Chicken' => 12.00,
            'Rice Balls & Okro' => 8.00,
            'Red Red (Beans & Plantain)' => 9.00,
            'Fried Rice & Vegetables' => 11.00,
            'Yam & Stew' => 10.00,
            'Kenkey & Fish' => 9.00,
            'Plain Rice & Gravy' => 8.00,
            'Spaghetti & Stew' => 10.00
        ];

        foreach ($meals as $mealName => $price) {
            Meal::firstOrCreate([
                'school_id' => $school->id,
                'name' => $mealName,
            ], [
                'description' => "Delicious {$mealName} prepared fresh daily",
                'calories' => rand(300, 600),
                'allergens' => 'May contain nuts, dairy',
                'ingredients' => 'Fresh ingredients, spices, oil',
                'is_active' => true,
            ]);
        }

        // Create schedules for current week and next 2 weeks
        $allMeals = Meal::where('school_id', $school->id)->get();
        
        for ($weekOffset = 0; $weekOffset < 3; $weekOffset++) {
            $weekStartDate = Carbon::now()->startOfWeek()->addWeeks($weekOffset);
            
            $daysOfWeek = [
                'monday' => ['Waakye & Egg', 'Banku & Fish'],
                'tuesday' => ['Jollof Rice & Chicken', 'Yam & Stew'],
                'wednesday' => ['Rice Balls & Okro', 'Kenkey & Fish'],
                'thursday' => ['Red Red (Beans & Plantain)', 'Fried Rice & Vegetables'],
                'friday' => ['Plain Rice & Gravy', 'Spaghetti & Stew']
            ];

            foreach ($daysOfWeek as $day => $mealOptions) {
                $selectedMealName = $mealOptions[array_rand($mealOptions)];
                $meal = $allMeals->where('name', $selectedMealName)->first();
                
                if ($meal) {
                    WeeklyMealSchedule::firstOrCreate([
                        'school_id' => $school->id,
                        'meal_id' => $meal->id,
                        'day_of_week' => $day,
                        'week_start_date' => $weekStartDate,
                    ], [
                        'price' => $meals[$selectedMealName],
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('Weekly meal schedules created successfully!');
    }
}
