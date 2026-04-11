<?php

namespace Database\Seeders;

use App\Models\FeedingPlan;
use App\Models\School;
use Illuminate\Database\Seeder;

class FeedingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();

        $feedingPlans = [
            [
                'name' => 'Daily Basic Meal Plan',
                'type' => 'daily',
                'price' => 10.00,
                'duration_days' => 1,
                'description' => 'Basic daily meal plan with standard portions',
                'school_name' => 'Merryland International School',
            ],
            [
                'name' => 'Weekly Standard Plan',
                'type' => 'weekly',
                'price' => 50.00,
                'duration_days' => 5,
                'description' => 'Complete weekly meal plan (Monday-Friday) with balanced nutrition',
                'school_name' => 'Merryland International School',
            ],
            [
                'name' => 'Premium Weekly Plan',
                'type' => 'weekly',
                'price' => 65.00,
                'duration_days' => 5,
                'description' => 'Enhanced weekly plan with larger portions and special meals',
                'school_name' => 'Merryland International School',
            ],
            [
                'name' => 'Monthly Meal Plan',
                'type' => 'termly',
                'price' => 200.00,
                'duration_days' => 20,
                'description' => 'Complete monthly meal plan with discounted rates',
                'school_name' => 'Premier Academy',
            ],
            [
                'name' => 'Daily Premium Plan',
                'type' => 'daily',
                'price' => 12.50,
                'duration_days' => 1,
                'description' => 'Premium daily meal with extra portions and dessert',
                'school_name' => 'Premier Academy',
            ],
            [
                'name' => 'Weekly Basic Plan',
                'type' => 'weekly',
                'price' => 45.00,
                'duration_days' => 5,
                'description' => 'Basic weekly meal plan for budget-conscious parents',
                'school_name' => 'Premier Academy',
            ],
            [
                'name' => 'Termly Comprehensive Plan',
                'type' => 'termly',
                'price' => 600.00,
                'duration_days' => 60,
                'description' => 'Complete term meal plan with maximum savings',
                'school_name' => 'Excellence College',
            ],
            [
                'name' => 'Weekly Vegetarian Plan',
                'type' => 'weekly',
                'price' => 48.00,
                'duration_days' => 5,
                'description' => 'Specialized vegetarian meal plan for the week',
                'school_name' => 'Excellence College',
            ],
            [
                'name' => 'Daily Student Plan',
                'type' => 'daily',
                'price' => 8.00,
                'duration_days' => 1,
                'description' => 'Affordable daily meal plan for students',
                'school_name' => 'Excellence College',
            ],
            [
                'name' => 'Weekly Executive Plan',
                'type' => 'weekly',
                'price' => 75.00,
                'duration_days' => 5,
                'description' => 'Executive weekly plan with premium meals and service',
                'school_name' => 'Excellence College',
            ],
        ];

        foreach ($feedingPlans as $planData) {
            $school = $schools->where('name', $planData['school_name'])->first();
            unset($planData['school_name']);

            FeedingPlan::firstOrCreate([
                'school_id' => $school->id,
                'name' => $planData['name'],
            ], array_merge($planData, [
                'school_id' => $school->id,
                'is_active' => true,
            ]));
        }

        $this->command->info('Feeding plans created successfully!');
    }
}
