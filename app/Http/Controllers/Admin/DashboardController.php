<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\School;
use App\Models\Student;
use App\Models\FeedingAttendance;
use App\Models\Guardian;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user?->hasRole('Super Admin')) {
            return $this->superAdmin();
        }
        if ($user?->hasRole('School Admin')) {
            return $this->schoolAdmin();
        }
        if ($user?->hasRole('Accountant')) {
            return $this->accountant();
        }
        if ($user?->hasRole('Parent')) {
            return $this->parent();
        }
        return view('dashboard');
    }

    public function superAdmin()
    {
        // Platform-wide stats
        $totalSchools = School::count();
        $totalPayments = Payment::count();
        $totalPlatformEarnings = Payment::sum('platform_fee');
        $totalAmountProcessed = Payment::sum('total_amount');
        
        // Monthly earnings for chart
        $monthlyEarnings = Payment::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(platform_fee) as earnings')
        )
        ->whereYear('created_at', now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->month => $item->earnings];
        });
        
        // Top schools by payment volume
        $topSchools = School::withCount('payments')
            ->withSum('payments', 'total_amount')
            ->orderByDesc('payments_sum_total_amount')
            ->limit(5)
            ->get();
        
        // Recent payments
        $recentPayments = Payment::with('school', 'guardian')
            ->latest()
            ->limit(10)
            ->get();
        
        return view('admin.dashboard.super-admin', compact(
            'totalSchools', 'totalPayments', 'totalPlatformEarnings', 
            'totalAmountProcessed', 'monthlyEarnings', 'topSchools', 'recentPayments'
        ));
    }

    public function schoolAdmin()
    {
        $school = auth()->user()->school;
        
        // Student stats
        $totalStudents = $school->students()->count();
        $paidStudents = $school->students()
            ->whereHas('feedingPlans', function ($query) {
                $query->where('student_plan.status', 'active');
            })->count();
        $unpaidStudents = $totalStudents - $paidStudents;
        
        // Today's attendance
        $today = now()->format('Y-m-d');
        $fedToday = FeedingAttendance::where('date', $today)
            ->where('status', 'fed')
            ->whereHas('student', function ($q) use ($school) {
                $q->where('school_id', $school->id);
            })->count();
        
        $notFedToday = FeedingAttendance::where('date', $today)
            ->where('status', 'not_fed')
            ->whereHas('student', function ($q) use ($school) {
                $q->where('school_id', $school->id);
            })->count();
        
        // Weekly attendance trend
        $weeklyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $fed = FeedingAttendance::where('date', $date)
                ->where('status', 'fed')
                ->whereHas('student', function ($q) use ($school) {
                    $q->where('school_id', $school->id);
                })->count();
            $weeklyStats[] = [
                'day' => now()->subDays($i)->format('D'),
                'fed' => $fed
            ];
        }
        
        // Recent payments for school
        $recentPayments = Payment::where('school_id', $school->id)
            ->with('guardian')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('admin.dashboard.school-admin', compact(
            'school', 'totalStudents', 'paidStudents', 'unpaidStudents',
            'fedToday', 'notFedToday', 'weeklyStats', 'recentPayments'
        ));
    }

    public function accountant()
    {
        $school = auth()->user()->school;
        
        // Payment stats
        $totalPayments = Payment::where('school_id', $school->id)->count();
        $completedPayments = Payment::where('school_id', $school->id)
            ->where('status', 'completed')->count();
        $pendingPayments = Payment::where('school_id', $school->id)
            ->where('status', 'pending')->count();
        $failedPayments = Payment::where('school_id', $school->id)
            ->where('status', 'failed')->count();
        
        // Amount stats
        $totalCollected = Payment::where('school_id', $school->id)
            ->where('status', 'completed')
            ->sum('school_amount');
        $pendingAmount = Payment::where('school_id', $school->id)
            ->where('status', 'pending')
            ->sum('school_amount');
        
        // Payment list
        $payments = Payment::where('school_id', $school->id)
            ->with('guardian', 'items.student')
            ->latest()
            ->paginate(10);
        
        return view('admin.dashboard.accountant', compact(
            'totalPayments', 'completedPayments', 'pendingPayments', 'failedPayments',
            'totalCollected', 'pendingAmount', 'payments'
        ));
    }

    public function parent()
    {
        $guardian = auth()->user()->guardian;
        
        if (!$guardian) {
            return view('admin.dashboard.parent', [
                'children' => collect(),
                'payments' => collect(),
                'totalSpent' => 0,
                'upcomingPayments' => collect(),
                'weekStartDate' => now()->startOfWeek(),
                'weeklySchedules' => collect(),
                'existingSelections' => collect(),
            ]);
        }
        
        // Children with feeding plans
        $children = $guardian->students()
            ->with(['feedingPlans' => function ($query) {
                $query->wherePivot('status', 'active');
            }, 'school'])
            ->get();
        
        // Payment history
        $payments = Payment::where('guardian_id', $guardian->id)
            ->with('items.student', 'school')
            ->latest()
            ->limit(10)
            ->get();
        
        // Total spent
        $totalSpent = Payment::where('guardian_id', $guardian->id)
            ->where('status', 'completed')
            ->sum('total_amount');
        
        // Upcoming payments (plans expiring soon)
        $upcomingPayments = $guardian->students()
            ->with(['feedingPlans' => function ($query) {
                $query->wherePivot('status', 'active')
                    ->wherePivot('end_date', '<=', now()->addDays(7));
            }])
            ->get();

        // Meal selection context for the dashboard section
        $weekStartDate = now()->startOfWeek();
        $weeklySchedules = collect();
        $existingSelections = collect();

        // If there is at least one child, preload that child's week's schedules and selections
        if ($children->isNotEmpty()) {
            $firstChild = $children->first();
            $weeklySchedules = \App\Models\WeeklyMealSchedule::where('school_id', $firstChild->school_id)
                ->forWeek($weekStartDate)
                ->active()
                ->with(['meal'])
                ->orderBy('day_of_week')
                ->get()
                ->groupBy('day_of_week');

            $existingSelections = \App\Models\MealSelection::whereIn('student_id', $children->pluck('id'))
                ->forWeek($weekStartDate)
                ->with(['weeklyMealSchedule.meal'])
                ->get()
                ->keyBy(function ($selection) {
                    return $selection->student_id . '_' . $selection->weeklyMealSchedule->day_of_week;
                });
        }
        
        return view('admin.dashboard.parent', compact(
            'children', 'payments', 'totalSpent', 'upcomingPayments',
            'weekStartDate', 'weeklySchedules', 'existingSelections'
        ));
    }

    public function children()
    {
        $guardian = auth()->user()->guardian;
        
        if (!$guardian) {
            return view('admin.children.index', [
                'children' => collect(),
            ]);
        }
        
        // Get all children for this parent with their details
        $children = $guardian->students()
            ->with(['school', 'feedingPlans' => function ($query) {
                $query->wherePivot('status', 'active');
            }])
            ->get();
        
        return view('admin.children.index', compact('children'));
    }
}
