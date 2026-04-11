<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignManageSettingsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Find the permission
        $permission = Permission::where('name', 'manage settings')
            ->where('guard_name', 'web')
            ->first();

        // Find School Admin role
        $schoolAdminRole = Role::where('name', 'School Admin')
            ->where('guard_name', 'web')
            ->first();

        // Assign permission to role if both exist
        if ($permission && $schoolAdminRole) {
            if (!$schoolAdminRole->hasPermissionTo($permission)) {
                $schoolAdminRole->givePermissionTo($permission);
                $this->command->info('Permission "manage settings" assigned to School Admin role.');
            } else {
                $this->command->info('School Admin role already has "manage settings" permission.');
            }
        } else {
            $this->command->error('Permission or role not found.');
        }

        // Also assign to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')
            ->where('guard_name', 'web')
            ->first();

        if ($permission && $superAdminRole) {
            if (!$superAdminRole->hasPermissionTo($permission)) {
                $superAdminRole->givePermissionTo($permission);
                $this->command->info('Permission "manage settings" assigned to Super Admin role.');
            } else {
                $this->command->info('Super Admin role already has "manage settings" permission.');
            }
        }
    }
}
