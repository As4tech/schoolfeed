<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // If we're in a tenant context, redirect to tenant login
        if ($request->route('school')) {
            // Build the URL manually since route('login') will try to use the global route
            return '/' . $request->route('school') . '/login';
        }
        
        // Otherwise, redirect to global login
        return route('login');
    }
}
