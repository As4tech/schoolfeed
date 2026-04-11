<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    public function index()
    {
        $guardians = Guardian::withCount('students')->paginate(10);
        return view('admin.guardians.index', compact('guardians'));
    }

    public function create()
    {
        return view('admin.guardians.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
        ]);

        $validated['school_id'] = auth()->user()->school_id;

        $guardian = Guardian::create($validated);

        // If email provided, offer to send invitation
        if ($guardian->email) {
            return redirect()->route('admin.guardians.index')
                ->with('success', 'Parent/Guardian created successfully. You can now send them an invitation to create their login credentials.')
                ->with('guardian_id', $guardian->id);
        }

        return redirect()->route('admin.guardians.index')
            ->with('success', 'Parent/Guardian created successfully. Note: No email provided, so you will need to create their user account manually.');
    }

    public function show(Guardian $guardian)
    {
        $guardian->load('students');
        return view('admin.guardians.show', compact('guardian'));
    }

    public function edit(Guardian $guardian)
    {
        return view('admin.guardians.edit', compact('guardian'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
        ]);

        $guardian->update($validated);

        return redirect()->route('admin.guardians.index')
            ->with('success', 'Parent/Guardian updated successfully.');
    }

    /**
     * Send invitation to guardian to create their account
     */
    public function sendInvitation(Guardian $guardian)
    {
        if (!$guardian->email) {
            return back()->with('error', 'Guardian must have an email address to send invitation.');
        }

        // Check if user already exists
        if ($guardian->user) {
            return back()->with('error', 'This guardian already has a user account.');
        }

        // Generate invitation token
        $token = \Illuminate\Support\Str::random(60);
        $guardian->invitation_token = $token;
        $guardian->invitation_expires_at = now()->addDays(7);
        $guardian->save();

        // Send invitation email
        // TODO: Implement email sending
        // For now, we'll show the link to admin
        
        $invitationLink = route('parent.invitation', ['token' => $token]);
        
        return back()->with('success', 'Invitation link generated. In production, this would be emailed to the guardian.')
            ->with('invitation_link', $invitationLink);
    }

    /**
     * Create user account for guardian manually
     */
    public function createAccount(Request $request, Guardian $guardian)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $guardian->name,
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'guardian_id' => $guardian->id,
            'school_id' => $guardian->school_id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('Parent');

        // Update guardian email if different
        if ($guardian->email !== $validated['email']) {
            $guardian->email = $validated['email'];
            $guardian->save();
        }

        return back()->with('success', 'User account created successfully for ' . $guardian->name);
    }

    public function destroy(Guardian $guardian)
    {
        // Also delete associated user if exists
        if ($guardian->user) {
            $guardian->user->delete();
        }

        $guardian->delete();

        return redirect()->route('admin.guardians.index')
            ->with('success', 'Parent/Guardian deleted successfully.');
    }
}
