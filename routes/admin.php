<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\GuardianController;
use App\Http\Controllers\Admin\MealController;
use App\Http\Controllers\Admin\FeedingPlanController;
use App\Http\Controllers\Admin\StudentPlanController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\FeedingAttendanceController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\InsightsController;
use App\Http\Controllers\Admin\DailyRosterController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\InventoryItemController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\StockOutController;
use App\Http\Controllers\Admin\KitchenUsageController;
use App\Http\Controllers\Admin\MealRecipeController;
use App\Http\Controllers\Admin\InventoryDashboardController;
use App\Http\Controllers\Admin\InventoryReportsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are protected by role-based middleware.
| Only authorized users can access these features.
|
*/

// Super Admin Routes - Full access to everything
Route::middleware(['role:Super Admin'])->group(function () {
    Route::get('/super-dashboard', [DashboardController::class, 'superAdmin'])
        ->name('super.dashboard');

    // User management
    Route::resource('users', UserController::class);

    // School management (avoid duplicate {school} param name under the slug prefix)
    Route::resource('schools', SchoolController::class)
        ->parameters(['schools' => 'managed_school']);

    // School approval toggles
    Route::patch('schools/{managed_school}/approve', [SchoolController::class, 'approve'])
        ->name('schools.approve');
    Route::patch('schools/{managed_school}/deactivate', [SchoolController::class, 'deactivate'])
        ->name('schools.deactivate');

    });

// School Admin Routes - Manage school-specific data
Route::middleware(['role:School Admin|Super Admin'])->group(function () {
    Route::get('/school-dashboard', [DashboardController::class, 'schoolAdmin'])
        ->name('school.dashboard');

    // Guardian/Parent management
    Route::resource('guardians', GuardianController::class);

    // Student management
    Route::resource('students', StudentController::class);
    Route::get('/students-import', [StudentController::class, 'showImportForm'])->name('students.import.form');
    Route::post('/students-import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students-template', [StudentController::class, 'downloadTemplate'])->name('students.template');

    // Feeding Plan management
    Route::resource('feeding-plans', FeedingPlanController::class);

    // Assign feeding plans to students
    Route::get('/students/{student}/plans/create', [StudentPlanController::class, 'create'])->name('students.plans.create');
    Route::post('/students/{student}/plans', [StudentPlanController::class, 'store'])->name('students.plans.store');
    Route::get('/students/{student}/plans/{planId}/edit', [StudentPlanController::class, 'edit'])->name('students.plans.edit');
    Route::put('/students/{student}/plans/{planId}', [StudentPlanController::class, 'update'])->name('students.plans.update');
    Route::delete('/students/{student}/plans/{planId}', [StudentPlanController::class, 'destroy'])->name('students.plans.destroy');

    // Meal management
    Route::resource('meals', MealController::class);

    // Classes management
    Route::resource('classes', SchoolClassController::class);

    // Weekly Meal Schedule management
    Route::get('/weekly-meal-schedules', [\App\Http\Controllers\Admin\WeeklyMealScheduleController::class, 'index'])->name('weekly-meal-schedules.index');
    Route::post('/weekly-meal-schedules', [\App\Http\Controllers\Admin\WeeklyMealScheduleController::class, 'store'])->name('weekly-meal-schedules.store');
    Route::get('/weekly-meal-schedules/show-week', [\App\Http\Controllers\Admin\WeeklyMealScheduleController::class, 'showWeek'])->name('weekly-meal-schedules.show-week');
    Route::post('/weekly-meal-schedules/copy-week', [\App\Http\Controllers\Admin\WeeklyMealScheduleController::class, 'copyWeek'])->name('weekly-meal-schedules.copy-week');

    // Daily Roster (paid students view)
    Route::get('/daily-roster', [DailyRosterController::class, 'index'])->name('daily-roster.index');
    Route::get('/daily-roster/export', [DailyRosterController::class, 'export'])->name('daily-roster.export');

    // Promotions (promote students between classes)
    Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
    Route::post('/promotions', [PromotionController::class, 'store'])->name('promotions.store');

    // Inventory & Kitchen Management (School Admin & Accountant)
    Route::middleware(['role:School Admin|Accountant|Super Admin'])->group(function () {
        // Dashboard
        Route::get('inventory', [InventoryDashboardController::class, 'index'])->name('inventory.dashboard');

        // Inventory Items
        Route::resource('inventory/items', InventoryItemController::class)->names('inventory.items');

        // Stock movements
        Route::get('inventory/stock-in/create', [StockInController::class, 'create'])->name('inventory.stock-in.create');
        Route::post('inventory/stock-in', [StockInController::class, 'store'])->name('inventory.stock-in.store');
        Route::get('inventory/stock-out/create', [StockOutController::class, 'create'])->name('inventory.stock-out.create');
        Route::post('inventory/stock-out', [StockOutController::class, 'store'])->name('inventory.stock-out.store');

        // Kitchen usage
        Route::get('kitchen/usages', [KitchenUsageController::class, 'index'])->name('kitchen.usages.index');
        Route::get('kitchen/usages/create', [KitchenUsageController::class, 'create'])->name('kitchen.usages.create');
        Route::post('kitchen/usages', [KitchenUsageController::class, 'store'])->name('kitchen.usages.store');

        // Meal recipes
        Route::get('recipes', [MealRecipeController::class, 'index'])->name('recipes.index');
        Route::post('recipes', [MealRecipeController::class, 'store'])->name('recipes.store');
        Route::delete('recipes/{recipe}', [MealRecipeController::class, 'destroy'])->name('recipes.destroy');

        // Inventory Reports
        Route::prefix('inventory/reports')->name('inventory.reports.')->group(function () {
            Route::get('daily-consumption', [InventoryReportsController::class, 'dailyConsumption'])->name('daily');
            Route::get('monthly-usage', [InventoryReportsController::class, 'monthlyUsage'])->name('monthly');
            Route::get('cost-analysis', [InventoryReportsController::class, 'costAnalysis'])->name('cost');
        });
    });

    // Feeding Attendance
    Route::get('/feeding-attendance', [FeedingAttendanceController::class, 'index'])->name('feeding-attendance.index');
    Route::post('/feeding-attendance', [FeedingAttendanceController::class, 'store'])->name('feeding-attendance.store');
    Route::post('/feeding-attendance/bulk', [FeedingAttendanceController::class, 'bulkStore'])->name('feeding-attendance.bulk');
    Route::get('/feeding-attendance/report', [FeedingAttendanceController::class, 'report'])->name('feeding-attendance.report');
});

// Accountant Routes - Manage payments and reports
Route::middleware(['role:Accountant|Super Admin|School Admin'])->group(function () {
    Route::get('/accountant-dashboard', [DashboardController::class, 'accountant'])
        ->name('accountant.dashboard');

    // Payment management
    Route::resource('payments', PaymentController::class);

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/daily', [ReportsController::class, 'daily'])->name('reports.daily');
    Route::get('/reports/monthly', [ReportsController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/unpaid', [ReportsController::class, 'unpaid'])->name('reports.unpaid');
    
    // Export routes
    Route::get('/reports/export/daily', [ReportsController::class, 'exportDaily'])->name('reports.export.daily');
    Route::get('/reports/export/monthly', [ReportsController::class, 'exportMonthly'])->name('reports.export.monthly');
    Route::get('/reports/export/unpaid', [ReportsController::class, 'exportUnpaid'])->name('reports.export.unpaid');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::get('/notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// Parent Routes - View children's data and make payments
Route::middleware(['role:Parent|Super Admin'])->group(function () {
    Route::get('/parent-dashboard', [DashboardController::class, 'parent'])
        ->name('parent.dashboard');

    // View children list
    Route::get('/my-children', [DashboardController::class, 'children'])
        ->name('parent.children');

    // View payments
    Route::get('/my-payments', [PaymentController::class, 'myPayments'])
        ->name('payments.mine');

    // Make payment
    Route::get('/make-payment', [PaymentController::class, 'create'])
        ->name('payments.create');
});

// Permission-based routes (more granular control)
Route::middleware(['permission:view reports'])->group(function () {
    Route::get('/reports/view', [\App\Http\Controllers\Admin\ReportController::class, 'view'])
        ->name('reports.view');
    Route::get('/reports/inventory', [\App\Http\Controllers\Admin\ReportController::class, 'inventory'])
        ->name('reports.inventory');
});

Route::middleware(['permission:export reports'])->group(function () {
    Route::post('/reports/download', [\App\Http\Controllers\Admin\ReportController::class, 'download'])
        ->name('reports.download');
});

// Settings Routes
Route::middleware(['permission:manage settings'])->group(function () {
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])
        ->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])
        ->name('settings.update');
    Route::post('/settings/remove-logo', [\App\Http\Controllers\Admin\SettingsController::class, 'removeLogo'])
        ->name('settings.remove-logo');
});
