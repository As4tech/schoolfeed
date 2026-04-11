<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedingAttendance;
use App\Models\Payment;
use App\Models\Student;
use App\Models\School;
use App\Exports\DailyReportExport;
use App\Exports\MonthlyRevenueExport;
use App\Exports\UnpaidStudentsExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function daily(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $class = $request->get('class');
        $schoolId = $this->getSchoolId();

        $query = FeedingAttendance::with(['student', 'markedBy'])
            ->where('date', $date)
            ->whereHas('student', function ($q) use ($schoolId) {
                if ($schoolId) {
                    $q->where('school_id', $schoolId);
                }
            });

        if ($class) {
            $query->whereHas('student', function ($q) use ($class) {
                $q->where('grade', $class);
            });
        }

        $attendance = $query->get();

        $stats = [
            'fed' => $attendance->where('status', 'fed')->count(),
            'not_fed' => $attendance->where('status', 'not_fed')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'total' => $attendance->count(),
        ];

        $classes = $this->getClasses($schoolId);

        return view('admin.reports.daily', compact('attendance', 'stats', 'date', 'class', 'classes'));
    }

    public function monthly(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $class = $request->get('class');
        $schoolId = $this->getSchoolId();

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $query = Payment::with(['guardian', 'school', 'items.student'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });

        if ($class) {
            $query->whereHas('items.student', function ($q) use ($class) {
                $q->where('grade', $class);
            });
        }

        $payments = $query->get();

        $stats = [
            'total_payments' => $payments->count(),
            'completed' => $payments->where('status', 'completed')->count(),
            'pending' => $payments->where('status', 'pending')->count(),
            'failed' => $payments->where('status', 'failed')->count(),
            'total_amount' => $payments->where('status', 'completed')->sum('total_amount'),
            'platform_fees' => $payments->where('status', 'completed')->sum('platform_fee'),
            'school_amount' => $payments->where('status', 'completed')->sum('school_amount'),
        ];

        $dailyBreakdown = $payments->where('status', 'completed')
            ->groupBy(fn($p) => $p->created_at->format('Y-m-d'))
            ->map(fn($day) => [
                'count' => $day->count(),
                'amount' => $day->sum('total_amount'),
            ]);

        $classes = $this->getClasses($schoolId);

        return view('admin.reports.monthly', compact('payments', 'stats', 'month', 'class', 'classes', 'dailyBreakdown'));
    }

    public function unpaid(Request $request)
    {
        $class = $request->get('class');
        $schoolId = $this->getSchoolId();

        $query = Student::with(['school', 'guardian', 'feedingPlans'])
            ->when($schoolId, function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })
            ->whereDoesntHave('feedingPlans', function ($q) {
                $q->where('student_plan.status', 'active');
            })
            ->orWhereHas('feedingPlans', function ($q) {
                $q->where('student_plan.end_date', '<', now());
            });

        if ($class) {
            $query->where('grade', $class);
        }

        $unpaidStudents = $query->paginate(50);
        $classes = $this->getClasses($schoolId);

        return view('admin.reports.unpaid', compact('unpaidStudents', 'class', 'classes'));
    }

    public function exportDaily(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $class = $request->get('class');
        $schoolId = $this->getSchoolId();
        $format = $request->get('format', 'xlsx');

        $filename = 'daily_report_' . $date . ($class ? '_class_' . $class : '') . '.' . $format;

        return Excel::download(
            new DailyReportExport($date, $class, $schoolId),
            $filename
        );
    }

    public function exportMonthly(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $class = $request->get('class');
        $schoolId = $this->getSchoolId();
        $format = $request->get('format', 'xlsx');

        $filename = 'monthly_revenue_' . $month . ($class ? '_class_' . $class : '') . '.' . $format;

        return Excel::download(
            new MonthlyRevenueExport($month, $class, $schoolId),
            $filename
        );
    }

    public function exportUnpaid(Request $request)
    {
        $class = $request->get('class');
        $schoolId = $this->getSchoolId();
        $format = $request->get('format', 'xlsx');

        $filename = 'unpaid_students_' . now()->format('Y-m-d') . ($class ? '_class_' . $class : '') . '.' . $format;

        return Excel::download(
            new UnpaidStudentsExport($class, $schoolId),
            $filename
        );
    }

    private function getSchoolId(): ?int
    {
        $user = auth()->user();

        if ($user->hasRole('Super Admin')) {
            return null;
        }

        return $user->school_id;
    }

    private function getClasses(?int $schoolId): array
    {
        $query = Student::distinct();

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->pluck('grade')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
