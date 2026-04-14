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
        $permission = Permission::firstOrCreate(['name' => 'manage settings']);

        $schoolAdminRole = Role::where('name', 'School Admin')->first();
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if ($schoolAdminRole) {
            $schoolAdminRole->givePermissionTo($permission);
        }

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
