<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ParentGuardianLinkSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing parent users created by RoleSeeder
        $lincolnParent = User::where('email', 'parent@lincoln.edu')->first();
        $washingtonParent = User::where('email', 'parent@washington.edu')->first();

        // Get schools
        $lincolnSchool = School::where('name', 'Lincoln High School')->first();
        $washingtonSchool = School::where('name', 'Washington Academy')->first();

        if (!$lincolnParent || !$washingtonParent) {
            $this->command->error('Parent users not found. Please run RoleSeeder first.');
            return;
        }

        // Create guardian records for existing parent users
        $lincolnGuardian = Guardian::firstOrCreate([
            'email' => 'parent@lincoln.edu',
            'school_id' => $lincolnSchool->id,
        ], [
            'name' => 'Lincoln Parent',
            'phone' => '0201234567',
            'address' => '123 Parent St, Accra',
            'occupation' => 'Parent',
        ]);

        $washingtonGuardian = Guardian::firstOrCreate([
            'email' => 'parent@washington.edu',
            'school_id' => $washingtonSchool->id,
        ], [
            'name' => 'Washington Parent',
            'phone' => '0509876543',
            'address' => '456 Family Ave, Kumasi',
            'occupation' => 'Parent',
        ]);

        // Link the parent users to their guardian records
        $lincolnParent->update(['guardian_id' => $lincolnGuardian->id]);
        $washingtonParent->update(['guardian_id' => $washingtonGuardian->id]);

        // Create students for these parents
        // Lincoln School students
        $lincolnStudents = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Lincoln',
                'student_id' => 'LHS100',
                'grade' => 'Grade 5',
                'gender' => 'Female',
                'date_of_birth' => '2014-05-15',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Bob',
                'last_name' => 'Lincoln',
                'student_id' => 'LHS101',
                'grade' => 'Grade 3',
                'gender' => 'Male',
                'date_of_birth' => '2016-08-22',
                'allergies' => 'Peanuts',
            ],
        ];

        foreach ($lincolnStudents as $studentData) {
            Student::firstOrCreate([
                'student_id' => $studentData['student_id'],
            ], array_merge($studentData, [
                'school_id' => $lincolnSchool->id,
                'parent_id' => $lincolnGuardian->id,
                'status' => 'enrolled',
                'emergency_contact_name' => $lincolnGuardian->name,
                'emergency_contact_phone' => $lincolnGuardian->phone,
            ]));
        }

        // Washington Academy students
        $washingtonStudents = [
            [
                'first_name' => 'Charlie',
                'last_name' => 'Washington',
                'student_id' => 'WAS100',
                'grade' => 'Grade 4',
                'gender' => 'Male',
                'date_of_birth' => '2015-11-10',
                'allergies' => 'Dairy',
            ],
            [
                'first_name' => 'Diana',
                'last_name' => 'Washington',
                'student_id' => 'WAS101',
                'grade' => 'Grade 2',
                'gender' => 'Female',
                'date_of_birth' => '2017-12-08',
                'allergies' => 'None',
            ],
        ];

        foreach ($washingtonStudents as $studentData) {
            Student::firstOrCreate([
                'student_id' => $studentData['student_id'],
            ], array_merge($studentData, [
                'school_id' => $washingtonSchool->id,
                'parent_id' => $washingtonGuardian->id,
                'status' => 'enrolled',
                'emergency_contact_name' => $washingtonGuardian->name,
                'emergency_contact_phone' => $washingtonGuardian->phone,
            ]));
        }

        // Assign feeding plans to these new students
        $lincolnGuardianStudents = Student::where('parent_id', $lincolnGuardian->id)->get();
        $washingtonGuardianStudents = Student::where('parent_id', $washingtonGuardian->id)->get();

        foreach ($lincolnGuardianStudents as $student) {
            $plan = \App\Models\FeedingPlan::where('school_id', $student->school_id)->first();
            if ($plan && !$student->feedingPlans()->where('feeding_plan_id', $plan->id)->exists()) {
                $student->feedingPlans()->attach($plan->id, [
                    'start_date' => now()->subDays(10),
                    'end_date' => now()->addDays(20),
                    'status' => 'active',
                    'amount_paid' => $plan->price,
                    'notes' => 'Regular payment',
                ]);
            }
        }

        foreach ($washingtonGuardianStudents as $student) {
            $plan = \App\Models\FeedingPlan::where('school_id', $student->school_id)->first();
            if ($plan && !$student->feedingPlans()->where('feeding_plan_id', $plan->id)->exists()) {
                $student->feedingPlans()->attach($plan->id, [
                    'start_date' => now()->subDays(5),
                    'end_date' => now()->addDays(25),
                    'status' => 'active',
                    'amount_paid' => $plan->price,
                    'notes' => 'Regular payment',
                ]);
            }
        }

        $this->command->info('Parent-Guardian links created successfully!');
        $this->command->info('Lincoln Parent (parent@lincoln.edu) now has 2 students');
        $this->command->info('Washington Parent (parent@washington.edu) now has 2 students');
    }
}
