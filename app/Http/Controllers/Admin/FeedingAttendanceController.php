<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedingAttendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeedingAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $parsedDate = Carbon::parse($date);
        $class = $request->get('class');
        
        // Get all students with active feeding plans (paid students)
        $students = Student::with(['feedingPlans' => function ($query) use ($parsedDate) {
            $query->wherePivot('status', 'active')
                ->wherePivot('start_date', '<=', $parsedDate)
                ->wherePivot('end_date', '>=', $parsedDate);
        }, 'attendance' => function ($query) use ($date) {
            $query->whereDate('date', $date);
        }])
        ->when($class, fn($q) => $q->where('grade', $class))
        ->whereHas('feedingPlans', function ($query) use ($parsedDate) {
            $query->where('student_plan.status', 'active')
                ->where('student_plan.start_date', '<=', $parsedDate)
                ->where('student_plan.end_date', '>=', $parsedDate);
        })
        ->orderBy('first_name')
        ->get();

        // Calculate stats
        $totalEligible = $students->count();
        $fedCount = $students->filter(fn($s) => $s->attendance?->status === 'fed')->count();
        $notFedCount = $students->filter(fn($s) => $s->attendance?->status === 'not_fed')->count();
        $absentCount = $students->filter(fn($s) => $s->attendance?->status === 'absent')->count();
        $pendingCount = $totalEligible - $fedCount - $notFedCount - $absentCount;
        
        // Build classes list for filter (distinct grades)
        $classes = Student::select('grade')->distinct()->orderBy('grade')->pluck('grade')->filter();

        return view('admin.feeding-attendance.index', compact(
            'students', 'date', 'class', 'classes', 'totalEligible', 'fedCount', 'notFedCount', 'absentCount', 'pendingCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:fed,not_fed,absent',
            'notes' => 'nullable|string',
        ]);

        $attendance = FeedingAttendance::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'marked_by' => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance recorded',
            'attendance' => $attendance,
            'status_label' => $attendance->status_label,
            'status_color' => $attendance->status_color,
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:fed,not_fed,absent',
            'attendance.*.notes' => 'nullable|string',
        ]);

        $date = $validated['date'];
        $records = [];

        foreach ($validated['attendance'] as $item) {
            $records[] = FeedingAttendance::updateOrCreate(
                [
                    'student_id' => $item['student_id'],
                    'date' => $date,
                ],
                [
                    'status' => $item['status'],
                    'notes' => $item['notes'] ?? null,
                    'marked_by' => auth()->id(),
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => count($records) . ' attendance records saved',
            'count' => count($records),
        ]);
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $attendance = FeedingAttendance::with(['student', 'markedBy'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = FeedingAttendance::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.feeding-attendance.report', compact(
            'attendance', 'startDate', 'endDate', 'stats'
        ));
    }
}
