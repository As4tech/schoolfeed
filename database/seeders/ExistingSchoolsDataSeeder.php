<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\School;
use App\Models\Student;
use App\Models\FeedingPlan;
use App\Models\Meal;
use App\Models\WeeklyMealSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class ExistingSchoolsDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing schools
        $lincolnSchool = School::where('name', 'Lincoln High School')->first();
        $washingtonSchool = School::where('name', 'Washington Academy')->first();

        if (!$lincolnSchool || !$washingtonSchool) {
            $this->command->error('Existing schools not found. Please run RoleSeeder first.');
            return;
        }

        // Create guardians for Lincoln High School
        $lincolnGuardians = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@email.com',
                'phone' => '0201234567',
                'address' => '123 Main St, Accra',
                'occupation' => 'Engineer',
            ],
            [
                'name' => 'Mary Johnson',
                'email' => 'mary.johnson@email.com',
                'phone' => '0249876543',
                'address' => '456 Oak Ave, Accra',
                'occupation' => 'Teacher',
            ],
        ];

        foreach ($lincolnGuardians as $guardianData) {
            $guardian = Guardian::firstOrCreate([
                'email' => $guardianData['email'],
                'school_id' => $lincolnSchool->id,
            ], array_merge($guardianData, ['school_id' => $lincolnSchool->id]));

            // Create user account for guardian
            User::firstOrCreate([
                'email' => $guardian->email,
            ], [
                'name' => $guardian->name,
                'password' => Hash::make('password'),
                'school_id' => $lincolnSchool->id,
            ])->assignRole('Parent');
        }

        // Create guardians for Washington Academy
        $washingtonGuardians = [
            [
                'name' => 'Robert Davis',
                'email' => 'robert.davis@email.com',
                'phone' => '0505551234',
                'address' => '789 Pine Rd, Kumasi',
                'occupation' => 'Doctor',
            ],
            [
                'name' => 'Susan Wilson',
                'email' => 'susan.wilson@email.com',
                'phone' => '0278889999',
                'address' => '321 Elm St, Kumasi',
                'occupation' => 'Nurse',
            ],
        ];

        foreach ($washingtonGuardians as $guardianData) {
            $guardian = Guardian::firstOrCreate([
                'email' => $guardianData['email'],
                'school_id' => $washingtonSchool->id,
            ], array_merge($guardianData, ['school_id' => $washingtonSchool->id]));

            // Create user account for guardian
            User::firstOrCreate([
                'email' => $guardian->email,
            ], [
                'name' => $guardian->name,
                'password' => Hash::make('password'),
                'school_id' => $washingtonSchool->id,
            ])->assignRole('Parent');
        }

        // Get created guardians
        $smith = Guardian::where('email', 'john.smith@email.com')->first();
        $johnson = Guardian::where('email', 'mary.johnson@email.com')->first();
        $davis = Guardian::where('email', 'robert.davis@email.com')->first();
        $wilson = Guardian::where('email', 'susan.wilson@email.com')->first();

        // Create students for Lincoln High School
        $lincolnStudents = [
            [
                'first_name' => 'Emma',
                'last_name' => 'Smith',
                'student_id' => 'LHS001',
                'grade' => 'Grade 5',
                'parent_id' => $smith->id,
                'gender' => 'Female',
                'date_of_birth' => '2014-03-15',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Smith',
                'student_id' => 'LHS002',
                'grade' => 'Grade 3',
                'parent_id' => $smith->id,
                'gender' => 'Male',
                'date_of_birth' => '2016-07-22',
                'allergies' => 'Peanuts',
            ],
            [
                'first_name' => 'Sophia',
                'last_name' => 'Johnson',
                'student_id' => 'LHS003',
                'grade' => 'Grade 4',
                'parent_id' => $johnson->id,
                'gender' => 'Female',
                'date_of_birth' => '2015-11-10',
                'allergies' => 'Dairy',
            ],
        ];

        foreach ($lincolnStudents as $studentData) {
            Student::firstOrCreate([
                'student_id' => $studentData['student_id'],
            ], array_merge($studentData, [
                'school_id' => $lincolnSchool->id,
                'status' => 'enrolled',
                'emergency_contact_name' => Guardian::find($studentData['parent_id'])->name,
                'emergency_contact_phone' => Guardian::find($studentData['parent_id'])->phone,
            ]));
        }

        // Create students for Washington Academy
        $washingtonStudents = [
            [
                'first_name' => 'Michael',
                'last_name' => 'Davis',
                'student_id' => 'WAS001',
                'grade' => 'Grade 6',
                'parent_id' => $davis->id,
                'gender' => 'Male',
                'date_of_birth' => '2013-09-25',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Olivia',
                'last_name' => 'Davis',
                'student_id' => 'WAS002',
                'grade' => 'Grade 2',
                'parent_id' => $davis->id,
                'gender' => 'Female',
                'date_of_birth' => '2017-12-08',
                'allergies' => 'Eggs',
            ],
            [
                'first_name' => 'William',
                'last_name' => 'Wilson',
                'student_id' => 'WAS003',
                'grade' => 'Grade 4',
                'parent_id' => $wilson->id,
                'gender' => 'Male',
                'date_of_birth' => '2015-04-05',
                'allergies' => 'None',
            ],
        ];

        foreach ($washingtonStudents as $studentData) {
            Student::firstOrCreate([
                'student_id' => $studentData['student_id'],
            ], array_merge($studentData, [
                'school_id' => $washingtonSchool->id,
                'status' => 'enrolled',
                'emergency_contact_name' => Guardian::find($studentData['parent_id'])->name,
                'emergency_contact_phone' => Guardian::find($studentData['parent_id'])->phone,
            ]));
        }

        // Create meals for both schools
        $meals = [
            'Jollof Rice & Chicken' => 12.00,
            'Banku & Tilapia' => 10.00,
            'Fried Rice & Vegetables' => 11.00,
            'Waakye & Stew' => 9.00,
            'Yam & Fried Egg' => 8.00,
        ];

        foreach ([$lincolnSchool, $washingtonSchool] as $school) {
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
        }

        // Create feeding plans for both schools
        $feedingPlans = [
            [
                'name' => 'Daily Basic Plan',
                'type' => 'daily',
                'price' => 8.00,
                'duration_days' => 1,
                'description' => 'Basic daily meal plan',
            ],
            [
                'name' => 'Weekly Standard Plan',
                'type' => 'weekly',
                'price' => 40.00,
                'duration_days' => 5,
                'description' => 'Complete weekly meal plan (Monday-Friday)',
            ],
        ];

        foreach ([$lincolnSchool, $washingtonSchool] as $school) {
            foreach ($feedingPlans as $planData) {
                FeedingPlan::firstOrCreate([
                    'school_id' => $school->id,
                    'name' => $planData['name'],
                ], array_merge($planData, [
                    'school_id' => $school->id,
                    'is_active' => true,
                ]));
            }
        }

        // Create weekly meal schedules for current week
        foreach ([$lincolnSchool, $washingtonSchool] as $school) {
            $allMeals = Meal::where('school_id', $school->id)->get();
            $weekStartDate = Carbon::now()->startOfWeek();
            
            $daysOfWeek = [
                'monday' => ['Jollof Rice & Chicken'],
                'tuesday' => ['Banku & Tilapia'],
                'wednesday' => ['Fried Rice & Vegetables'],
                'thursday' => ['Waakye & Stew'],
                'friday' => ['Yam & Fried Egg']
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

        // Assign feeding plans to students
        $allStudents = Student::all();
        foreach ($allStudents as $student) {
            $schoolFeedingPlans = FeedingPlan::where('school_id', $student->school_id)->get();
            
            // Assign 1 feeding plan per student
            $plan = $schoolFeedingPlans->random();
            $startDate = Carbon::now()->subDays(rand(0, 30));
            $endDate = $startDate->copy()->addDays($plan->duration_days);
            
            $status = rand(0, 1) ? 'active' : 'completed';
            if ($status === 'completed') {
                $endDate = Carbon::now()->subDays(rand(1, 10));
            }

            $student->feedingPlans()->attach($plan->id, [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
                'amount_paid' => $status === 'active' ? $plan->price : $plan->price * 0.8,
                'notes' => $status === 'active' ? 'Regular payment' : 'Plan completed, renewal required',
            ]);
        }

        $this->command->info('Existing schools data populated successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Lincoln Parent: john.smith@email.com / password');
        $this->command->info('Lincoln Parent: mary.johnson@email.com / password');
        $this->command->info('Washington Parent: robert.davis@email.com / password');
        $this->command->info('Washington Parent: susan.wilson@email.com / password');
    }
}
