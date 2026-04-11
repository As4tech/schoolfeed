<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class GuardianSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();
        $parentRole = Role::firstOrCreate(['name' => 'Parent']);

        $guardians = [
            [
                'name' => 'Mr. Kwame Asante',
                'email' => 'kwame.asante@gmail.com',
                'phone' => '0241234567',
                'address' => 'Tema Community 1, Accra',
                'occupation' => 'Engineer',
                'school_name' => 'Merryland International School',
            ],
            [
                'name' => 'Mrs. Ama Boateng',
                'email' => 'ama.boateng@yahoo.com',
                'phone' => '0209876543',
                'address' => 'East Legon, Accra',
                'occupation' => 'Teacher',
                'school_name' => 'Merryland International School',
            ],
            [
                'name' => 'Mr. Yaw Mensah',
                'email' => 'yaw.mensah@hotmail.com',
                'phone' => '0505551234',
                'address' => 'Kumasi, Ashanti Region',
                'occupation' => 'Businessman',
                'school_name' => 'Premier Academy',
            ],
            [
                'name' => 'Mrs. Efua Agyeman',
                'email' => 'efua.agyeman@gmail.com',
                'phone' => '0278889999',
                'address' => 'Takoradi, Western Region',
                'occupation' => 'Nurse',
                'school_name' => 'Excellence College',
            ],
            [
                'name' => 'Mr. Kofi Osei',
                'email' => 'kofi.osei@yahoo.com',
                'phone' => '0234445555',
                'address' => 'Cape Coast, Central Region',
                'occupation' => 'Banker',
                'school_name' => 'Excellence College',
            ],
        ];

        foreach ($guardians as $guardianData) {
            $school = $schools->where('name', $guardianData['school_name'])->first();
            unset($guardianData['school_name']);

            $guardian = Guardian::firstOrCreate([
                'email' => $guardianData['email'],
                'school_id' => $school->id,
            ], array_merge($guardianData, ['school_id' => $school->id]));

            // Create user account for guardian
            $user = User::firstOrCreate([
                'email' => $guardian->email,
            ], [
                'name' => $guardian->name,
                'password' => Hash::make('password'),
                'school_id' => $school->id,
            ]);

            $user->assignRole($parentRole);
        }

        $this->command->info('Guardians and parent accounts created successfully!');
    }
}
