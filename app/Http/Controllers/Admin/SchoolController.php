<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::latest()->paginate(10);
        return view('admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('admin.schools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools',
            'paystack_subaccount_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ]);

        $school = School::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'paystack_subaccount_code' => $validated['paystack_subaccount_code'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('superadmin.schools.index')
            ->with('success', 'School created successfully.');
    }

    public function show(School $managed_school, ?School $school = null)
    {
        return view('admin.schools.show', ['school' => $managed_school]);
    }

    public function edit(School $managed_school, ?School $school = null)
    {
        return view('admin.schools.edit', ['school' => $managed_school]);
    }

    public function update(Request $request, School $managed_school, ?School $school = null)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email,' . $managed_school->id,
            'paystack_subaccount_code' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $managed_school->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'paystack_subaccount_code' => $validated['paystack_subaccount_code'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('superadmin.schools.index')
            ->with('success', 'School updated successfully.');
    }

    public function destroy(School $managed_school, ?School $school = null)
    {
        $managed_school->delete();
        return redirect()->route('superadmin.schools.index')
            ->with('success', 'School deleted successfully.');
    }

    public function approve(School $managed_school, ?School $school = null)
    {
        $managed_school->update(['is_active' => true]);
        
        // Activate the school admin user
        $adminUser = $managed_school->users()->whereHas('roles', function($query) {
            $query->where('name', 'School Admin');
        })->first();
        
        if ($adminUser) {
            $adminUser->update(['email_verified_at' => now()]);
        }
        
        // Notify school by email
        if (!empty($managed_school->email)) {
            Mail::to($managed_school->email)->queue(new \App\Mail\SchoolApprovedMail($managed_school, $adminUser));
        }
        return back()->with('success', 'School approved and activated.');
    }

    public function deactivate(School $managed_school, ?School $school = null)
    {
        $managed_school->update(['is_active' => false]);
        if (!empty($managed_school->email)) {
            Mail::to($managed_school->email)->queue(new \App\Mail\SchoolDeactivatedMail($managed_school));
        }
        return back()->with('success', 'School deactivated.');
    }
}
