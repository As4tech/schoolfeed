<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $schools = School::all();
        $guardians = Guardian::all();

        $students = [
            [
                'first_name' => 'Kofi',
                'last_name' => 'Mensah',
                'student_id' => 'STU001',
                'grade' => 'Class 3',
                'parent_email' => 'kwame.asante@gmail.com',
                'school_name' => 'Merryland International School',
                'gender' => 'Male',
                'date_of_birth' => '2016-03-15',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Ama',
                'last_name' => 'Mensah',
                'student_id' => 'STU002',
                'grade' => 'Class 1',
                'parent_email' => 'kwame.asante@gmail.com',
                'school_name' => 'Merryland International School',
                'gender' => 'Female',
                'date_of_birth' => '2018-07-22',
                'allergies' => 'Peanuts',
            ],
            [
                'first_name' => 'Yaw',
                'last_name' => 'Boateng',
                'student_id' => 'STU003',
                'grade' => 'Class 2',
                'parent_email' => 'ama.boateng@yahoo.com',
                'school_name' => 'Merryland International School',
                'gender' => 'Male',
                'date_of_birth' => '2017-11-10',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Akosua',
                'last_name' => 'Osei',
                'student_id' => 'STU004',
                'grade' => 'Class 4',
                'parent_email' => 'yaw.mensah@hotmail.com',
                'school_name' => 'Premier Academy',
                'gender' => 'Female',
                'date_of_birth' => '2015-05-18',
                'allergies' => 'Dairy',
            ],
            [
                'first_name' => 'Kwabena',
                'last_name' => 'Agyeman',
                'student_id' => 'STU005',
                'grade' => 'Class 5',
                'parent_email' => 'efua.agyeman@gmail.com',
                'school_name' => 'Excellence College',
                'gender' => 'Male',
                'date_of_birth' => '2014-09-25',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Yaa',
                'last_name' => 'Osei',
                'student_id' => 'STU006',
                'grade' => 'Class 3',
                'parent_email' => 'kofi.osei@yahoo.com',
                'school_name' => 'Excellence College',
                'gender' => 'Female',
                'date_of_birth' => '2016-12-08',
                'allergies' => 'Eggs',
            ],
            [
                'first_name' => 'Kojo',
                'last_name' => 'Asante',
                'student_id' => 'STU007',
                'grade' => 'Class 1',
                'parent_email' => 'ama.boateng@yahoo.com',
                'school_name' => 'Merryland International School',
                'gender' => 'Male',
                'date_of_birth' => '2018-02-14',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Adwoa',
                'last_name' => 'Mensah',
                'student_id' => 'STU008',
                'grade' => 'Class 2',
                'parent_email' => 'yaw.mensah@hotmail.com',
                'school_name' => 'Premier Academy',
                'gender' => 'Female',
                'date_of_birth' => '2017-06-30',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Kwaku',
                'last_name' => 'Dankwa',
                'student_id' => 'STU009',
                'grade' => 'Class 4',
                'parent_email' => 'kwame.asante@gmail.com',
                'school_name' => 'Merryland International School',
                'gender' => 'Male',
                'date_of_birth' => '2015-08-12',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Abena',
                'last_name' => 'Ofori',
                'student_id' => 'STU010',
                'grade' => 'Class 5',
                'parent_email' => 'efua.agyeman@gmail.com',
                'school_name' => 'Excellence College',
                'gender' => 'Female',
                'date_of_birth' => '2014-11-28',
                'allergies' => 'Lactose',
            ],
            [
                'first_name' => 'Kwame',
                'last_name' => 'Yeboah',
                'student_id' => 'STU011',
                'grade' => 'Class 3',
                'parent_email' => 'kofi.osei@yahoo.com',
                'school_name' => 'Excellence College',
                'gender' => 'Male',
                'date_of_birth' => '2016-04-05',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Afia',
                'last_name' => 'Asiedu',
                'student_id' => 'STU012',
                'grade' => 'Class 2',
                'parent_email' => 'ama.boateng@yahoo.com',
                'school_name' => 'Merryland International School',
                'gender' => 'Female',
                'date_of_birth' => '2017-09-17',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Yaw',
                'last_name' => 'Owusu',
                'student_id' => 'STU013',
                'grade' => 'Class 1',
                'parent_email' => 'yaw.mensah@hotmail.com',
                'school_name' => 'Premier Academy',
                'gender' => 'Male',
                'date_of_birth' => '2018-01-23',
                'allergies' => 'Wheat',
            ],
            [
                'first_name' => 'Akua',
                'last_name' => 'Frimpong',
                'student_id' => 'STU014',
                'grade' => 'Class 4',
                'parent_email' => 'kofi.osei@yahoo.com',
                'school_name' => 'Excellence College',
                'gender' => 'Female',
                'date_of_birth' => '2015-07-30',
                'allergies' => 'None',
            ],
            [
                'first_name' => 'Kojo',
                'last_name' => 'Appiah',
                'student_id' => 'STU015',
                'grade' => 'Class 5',
                'parent_email' => 'kwame.asante@gmail.com',
                'school_name' => 'Merryland International School',
                'gender' => 'Male',
                'date_of_birth' => '2014-12-14',
                'allergies' => 'Soy',
            ],
        ];

        foreach ($students as $studentData) {
            $school = $schools->where('name', $studentData['school_name'])->first();
            $guardian = $guardians->where('email', $studentData['parent_email'])->first();
            
            unset($studentData['school_name'], $studentData['parent_email']);

            Student::firstOrCreate([
                'student_id' => $studentData['student_id'],
            ], array_merge($studentData, [
                'school_id' => $school->id,
                'parent_id' => $guardian->id,
                'emergency_contact_name' => $guardian->name,
                'emergency_contact_phone' => $guardian->phone,
                'status' => 'enrolled',
            ]));
        }

        $this->command->info('Students created successfully!');
    }
}
