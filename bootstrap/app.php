<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Global auth routes that don't need tenant context (logout, profile, password)
            Route::middleware(['web'])->group(function () {
                Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])
                    ->name('logout');
                Route::put('password', [\App\Http\Controllers\Auth\PasswordController::class, 'update'])
                    ->name('password.update');
            });

            // Super Admin login route (no tenant context)
            Route::prefix('super-admin')
                ->middleware(['web'])
                ->group(function () {
                    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
                        ->name('superadmin.login');
                    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])
                        ->name('superadmin.login.store');
                    
                    // Password reset routes
                    Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
                        ->name('superadmin.password.request');
                    Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
                        ->name('superadmin.password.email');
                    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
                        ->name('superadmin.password.reset');
                    Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
                        ->name('superadmin.password.update');
                });

            // Global Super Admin routes (no tenant slug required) - REGISTER FIRST
            Route::prefix('super-admin')
                ->middleware(['web', 'auth', 'role:Super Admin'])
                ->name('superadmin.')
                ->group(function () {
                    // Super Admin dashboard
                    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'superAdmin'])
                        ->name('dashboard');
                    
                    // Schools management outside tenant context
                    Route::resource('schools', \App\Http\Controllers\Admin\SchoolController::class)
                        ->parameters(['schools' => 'managed_school']);
                    Route::patch('schools/{managed_school}/approve', [\App\Http\Controllers\Admin\SchoolController::class, 'approve'])
                        ->name('schools.approve');
                    Route::patch('schools/{managed_school}/deactivate', [\App\Http\Controllers\Admin\SchoolController::class, 'deactivate'])
                        ->name('schools.deactivate');
                    
                    // Insights - Super Admin only
                    Route::get('/insights/schools', [\App\Http\Controllers\Admin\InsightsController::class, 'schools'])
                        ->name('insights.schools');
                });

            // Guest/auth routes under school slug context (login, register, password reset)
            Route::prefix('{school:slug}')
                ->middleware(['web', 'school.context'])
                ->group(function () {
                    Route::middleware('guest')->group(function () {
                        Route::get('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
                            ->name('register');
                        Route::post('register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
                        Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])
                            ->name('login');
                        Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
                        Route::get('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])
                            ->name('password.request');
                        Route::post('forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])
                            ->name('password.email');
                        Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])
                            ->name('password.reset');
                        Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
                            ->name('password.store');
                    });

                    Route::middleware('auth')->group(function () {
                        Route::get('verify-email', [\App\Http\Controllers\Auth\EmailVerificationPromptController::class])
                            ->name('verification.notice');
                        Route::get('verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmailController::class])
                            ->middleware(['signed', 'throttle:6,1'])
                            ->name('verification.verify');
                        Route::post('email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
                            ->middleware('throttle:6,1')
                            ->name('verification.send');
                        Route::get('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])
                            ->name('password.confirm');
                        Route::post('confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);
                                            });
                });

            // Authenticated general routes under school slug
            Route::prefix('{school:slug}')
                ->middleware(['web', 'school.context', 'auth', 'role:Super Admin|School Admin|Accountant|Parent'])
                ->group(function () {
                    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
                });

            // Admin routes under school slug context
            Route::prefix('{school:slug}')
                ->middleware(['web', 'school.context', 'auth', \Illuminate\Routing\Middleware\SubstituteBindings::class])
                ->name('admin.')
                ->group(function () {
                    Route::prefix('admin')->group(base_path('routes/admin.php'));
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register Spatie Permission Middleware
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'school.context' => \App\Http\Middleware\SetSchoolContext::class,
        ]);
        
        // Use custom Authenticate middleware for proper tenant login redirects
        $middleware->replace(\Illuminate\Auth\Middleware\Authenticate::class, \App\Http\Middleware\Authenticate::class);
        
        // Override default guest redirect to handle school context
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->route('school')) {
                return '/' . $request->route('school') . '/login';
            }
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
