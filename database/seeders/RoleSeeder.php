<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\School;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // School management
            'view schools',
            'create schools',
            'edit schools',
            'delete schools',

            // Student management
            'view students',
            'create students',
            'edit students',
            'delete students',

            // Meal management
            'view meals',
            'create meals',
            'edit meals',
            'delete meals',

            // Payment management
            'view payments',
            'create payments',
            'edit payments',
            'delete payments',

            // Reports
            'view reports',
            'export reports',

            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // School Admin - manages school-specific data
        $schoolAdminRole = Role::create(['name' => 'School Admin']);
        $schoolAdminRole->givePermissionTo([
            'view students',
            'create students',
            'edit students',
            'view meals',
            'create meals',
            'edit meals',
            'view payments',
            'create payments',
            'edit payments',
            'view reports',
            'export reports',
        ]);

        // Accountant - manages payments and reports
        $accountantRole = Role::create(['name' => 'Accountant']);
        $accountantRole->givePermissionTo([
            'view students',
            'view payments',
            'create payments',
            'edit payments',
            'view reports',
            'export reports',
        ]);

        // Parent - views their children's data and makes payments
        $parentRole = Role::create(['name' => 'Parent']);
        $parentRole->givePermissionTo([
            'view students',
            'view meals',
            'view payments',
            'create payments',
        ]);

        // Create sample schools and assign users
        $this->createSchoolsAndUsers();

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Sample schools created with users!');
        $this->command->info('Default users created with passwords: password');
    }

    private function createSchoolsAndUsers(): void
    {
        // Create School 1
        $school1 = School::create([
            'name' => 'Lincoln High School',
            'email' => 'admin@lincoln.edu',
            'phone' => '+1234567890',
            'address' => '123 Education Ave, Learning City',
            'paystack_subaccount_code' => 'ACCT_lincoln_123',
            'is_active' => true,
        ]);

        // Create School 2
        $school2 = School::create([
            'name' => 'Washington Academy',
            'email' => 'admin@washington.edu',
            'phone' => '+0987654321',
            'address' => '456 Knowledge St, Wisdom Town',
            'paystack_subaccount_code' => 'ACCT_washington_456',
            'is_active' => true,
        ]);

        // Super Admin (no school - can access all)
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@schoolfeed.local',
            'password' => bcrypt('password'),
            'school_id' => null,
        ]);
        $superAdmin->assignRole('Super Admin');

        // School 1 Users
        $school1Admin = User::create([
            'name' => 'Lincoln School Admin',
            'email' => 'schooladmin@lincoln.edu',
            'password' => bcrypt('password'),
            'school_id' => $school1->id,
        ]);
        $school1Admin->assignRole('School Admin');

        $school1Accountant = User::create([
            'name' => 'Lincoln Accountant',
            'email' => 'accountant@lincoln.edu',
            'password' => bcrypt('password'),
            'school_id' => $school1->id,
        ]);
        $school1Accountant->assignRole('Accountant');

        $school1Parent = User::create([
            'name' => 'Lincoln Parent',
            'email' => 'parent@lincoln.edu',
            'password' => bcrypt('password'),
            'school_id' => $school1->id,
        ]);
        $school1Parent->assignRole('Parent');

        // School 2 Users
        $school2Admin = User::create([
            'name' => 'Washington School Admin',
            'email' => 'schooladmin@washington.edu',
            'password' => bcrypt('password'),
            'school_id' => $school2->id,
        ]);
        $school2Admin->assignRole('School Admin');

        $school2Accountant = User::create([
            'name' => 'Washington Accountant',
            'email' => 'accountant@washington.edu',
            'password' => bcrypt('password'),
            'school_id' => $school2->id,
        ]);
        $school2Accountant->assignRole('Accountant');

        $school2Parent = User::create([
            'name' => 'Washington Parent',
            'email' => 'parent@washington.edu',
            'password' => bcrypt('password'),
            'school_id' => $school2->id,
        ]);
        $school2Parent->assignRole('Parent');
    }
}
