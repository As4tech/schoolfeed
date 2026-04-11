<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckSettingsAuth
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('Settings auth check', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user() ? Auth::user()->email : 'not authenticated',
            'has_permission' => Auth::user() ? Auth::user()->hasPermissionTo('manage settings') : 'no user',
            'route' => $request->route()->getName(),
        ]);
        
        return $next($request);
    }
}
