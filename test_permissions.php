<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the currently authenticated user from session
$user = auth()->user();

if ($user) {
    echo "User ID: " . $user->id . "\n";
    echo "User Email: " . $user->email . "\n";
    echo "User Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "Has 'manage settings' permission: " . ($user->hasPermissionTo('manage settings') ? 'YES' : 'NO') . "\n";
    echo "School ID: " . $user->school_id . "\n";
    
    // Check all permissions
    echo "\nAll Permissions:\n";
    foreach ($user->getAllPermissions() as $permission) {
        echo "- " . $permission->name . "\n";
    }
} else {
    echo "No authenticated user found\n";
}
