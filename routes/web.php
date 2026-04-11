<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\School;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public: School registration landing/form
Route::get('/register-school', function () {
    return view('auth.register-school');
})->name('schools.register');

Route::post('/register-school', function () {
    $validated = request()->validate([
        'name' => ['required','string','max:255'],
        'email' => ['required','email','max:255','unique:schools,email'],
        'phone' => ['nullable','string','max:255'],
        'address' => ['nullable','string'],
        'slug' => ['nullable','string','max:255'],
        // Admin credentials
        'admin_name' => ['required','string','max:255'],
        'admin_email' => ['required','email','max:255','unique:users,email'],
        'admin_password' => ['required','string','min:8','confirmed'],
    ]);

    $slug = trim($validated['slug'] ?? '') !== '' ? Str::slug($validated['slug']) : Str::slug($validated['name']);
    $base = $slug;
    $i = 1;
    while (School::where('slug', $slug)->exists()) {
        $slug = $base.'-'.$i++;
    }

    $school = School::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone' => $validated['phone'] ?? null,
        'address' => $validated['address'] ?? null,
        // Newly registered schools require approval by Super Admin
        'is_active' => false,
        'slug' => $slug,
    ]);

    // Create the admin user (inactive until school is approved)
    $user = \App\Models\User::create([
        'name' => $validated['admin_name'],
        'email' => $validated['admin_email'],
        'password' => bcrypt($validated['admin_password']),
        'school_id' => $school->id,
        'email_verified_at' => null, // Will verify on approval
    ]);

    // Assign School Admin role
    $user->assignRole('School Admin');

    return redirect()->route('schools.register.submitted', ['slug' => $school->slug])
        ->with('success', 'Registration submitted successfully and is awaiting approval.');
});

// Registration confirmation page
Route::get('/register-school/submitted', function () {
    $slug = request('slug');
    return view('auth.register-school-submitted', [
        'slug' => $slug,
    ]);
})->name('schools.register.submitted');

// Dashboard is now provided under slug prefix in bootstrap/app.php

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Parent meal selection routes
    Route::get('/parent/meals', [\App\Http\Controllers\Parent\ParentMealController::class, 'index'])->name('parent.meals.index');
    Route::post('/parent/meals', [\App\Http\Controllers\Parent\ParentMealController::class, 'store'])->name('parent.meals.store');
    Route::post('/parent/meals/payment', [\App\Http\Controllers\Parent\ParentMealController::class, 'payment'])->name('parent.meals.payment');
    Route::get('/parent/meals/week-meals', [\App\Http\Controllers\Parent\ParentMealController::class, 'getWeekMeals'])->name('parent.meals.week-meals');
    
    // Paystack payment routes (non-schooled legacy)
    Route::get('/payment/callback', [\App\Http\Controllers\Admin\PaymentController::class, 'handleCallback'])->name('payment.callback');
    Route::get('/payment/success/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'success'])->name('payment.success');
});

// Auth routes are now loaded under the school slug in bootstrap/app.php
