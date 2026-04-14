<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if this is a super-admin login (no school context)
        if (auth()->user()->hasRole('Super Admin') && !request()->route('school')) {
            return redirect()->intended(route('superadmin.dashboard', absolute: false));
        }

        // For parents, redirect to their tenant-scoped dashboard
        if (auth()->user()->hasRole('Parent') && auth()->user()->guardian_id) {
            $guardian = \App\Models\Guardian::find(auth()->user()->guardian_id);
            if ($guardian && $guardian->school) {
                return redirect()->intended(route('dashboard', ['school' => $guardian->school->slug], absolute: false));
            }
        }

        // For school admins and other roles, redirect to school dashboard
        if (auth()->user()->school_id) {
            $school = \App\Models\School::find(auth()->user()->school_id);
            if ($school) {
                return redirect()->intended(route('dashboard', ['school' => $school->slug], absolute: false));
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
