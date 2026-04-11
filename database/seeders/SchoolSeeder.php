<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        // Create schools
        $schools = [
            [
                'name' => 'Merryland International School',
                'address' => '123 Accra Street, East Legon, Accra',
                'phone' => '0301234567',
                'email' => 'info@merryland.edu.gh',
            ],
            [
                'name' => 'Premier Academy',
                'address' => '456 Kumasi Road, Asokwa, Kumasi',
                'phone' => '0329876543',
                'email' => 'admin@premieracademy.edu.gh',
            ],
            [
                'name' => 'Excellence College',
                'address' => '789 Takoradi Avenue, Sekondi, Takoradi',
                'phone' => '0314567890',
                'email' => 'contact@excellencecollege.edu.gh',
            ],
        ];

        foreach ($schools as $schoolData) {
            School::firstOrCreate(['email' => $schoolData['email']], $schoolData);
        }

        // Create school admin users for each school
        $schools = School::all();
        $schoolAdminRole = Role::firstOrCreate(['name' => 'School Admin']);

        foreach ($schools as $index => $school) {
            $schoolDomain = 'merryland';
            if ($school->name == 'Premier Academy') {
                $schoolDomain = 'premier';
            } elseif ($school->name == 'Excellence College') {
                $schoolDomain = 'excellence';
            }
            
            $user = User::firstOrCreate([
                'email' => "admin" . ($index + 1) . "@{$schoolDomain}.edu.gh"
            ], [
                'name' => "Admin {$school->name}",
                'password' => Hash::make('password'),
                'school_id' => $school->id,
            ]);

            $user->assignRole($schoolAdminRole);
        }

        $this->command->info('Schools and school admins created successfully!');
    }
}
