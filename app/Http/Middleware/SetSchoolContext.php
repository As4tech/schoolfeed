<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetSchoolContext
{
    public function handle(Request $request, Closure $next)
    {
        // Ensure URL defaults include the school slug as early as possible
        $param = $request->route('school');
        $resolvedSchool = null;
        $slug = null;

        if ($param instanceof School) {
            $resolvedSchool = $param;
            $slug = $param->slug;
        } elseif (is_string($param) && $param !== '') {
            $slug = $param;
            $resolvedSchool = School::where('slug', $slug)->first();
        }

        if ($slug) {
            URL::defaults(['school' => $slug]);
        }

        if ($resolvedSchool instanceof School) {
            app()->instance('school', $resolvedSchool);

            if (auth()->check() && auth()->user()->school_id !== $resolvedSchool->id && !auth()->user()->hasRole('Super Admin')) {
                abort(403, 'You do not have access to this school.');
            }
        }

        return $next($request);
    }
}
