<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        // Create the manage settings permission
        $permission = Permission::create([
            'name' => 'manage settings',
            'guard_name' => 'web'
        ]);

        // Assign permission to School Admin role
        $schoolAdminRole = Role::where('name', 'School Admin')->where('guard_name', 'web')->first();
        if ($schoolAdminRole) {
            $schoolAdminRole->givePermissionTo($permission);
        }

        // Also assign to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        // Remove permission from roles
        $permission = Permission::where('name', 'manage settings')->where('guard_name', 'web')->first();
        
        if ($permission) {
            $schoolAdminRole = Role::where('name', 'School Admin')->where('guard_name', 'web')->first();
            if ($schoolAdminRole) {
                $schoolAdminRole->revokePermissionTo($permission);
            }

            $superAdminRole = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
            if ($superAdminRole) {
                $superAdminRole->revokePermissionTo($permission);
            }

            // Delete the permission
            $permission->delete();
        }
    }
};
