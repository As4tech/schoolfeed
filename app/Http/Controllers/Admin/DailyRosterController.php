<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\PaymentItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyRosterController extends Controller
{
    public function index(Request $request)
    {
        $date = Carbon::parse($request->get('date', now()->format('Y-m-d')));
        $weekStart = (clone $date)->startOfWeek(Carbon::MONDAY);
        $days = [
            'M' => (clone $weekStart)->copy(),
            'T' => (clone $weekStart)->copy()->addDays(1),
            'W' => (clone $weekStart)->copy()->addDays(2),
            'T2' => (clone $weekStart)->copy()->addDays(3),
            'F' => (clone $weekStart)->copy()->addDays(4),
        ];

        $query = Student::query();

        if ($q = $request->get('q')) {
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('first_name', 'like', "%$q%")
                    ->orWhere('last_name', 'like', "%$q%")
                    ->orWhere('student_id', 'like', "%$q%");
            });
        }

        if ($grade = $request->get('class')) {
            $query->where('grade', $grade);
        }

        // Determine paid/not paid for selected date based on successful payments on that date
        $status = $request->get('status'); // 'paid' | 'not paid' | null

        $students = $query->orderBy('first_name')->paginate(15)->withQueryString();

        // Prefetch week payments for listed students to avoid N+1
        $studentIds = $students->pluck('id')->all();
        $weekEnd = (clone $weekStart)->copy()->addDays(4)->endOfDay();

        $items = PaymentItem::with(['payment' => function ($q) use ($weekStart, $weekEnd) {
                $q->where('status', 'completed')
                  ->whereBetween('paid_at', [$weekStart->copy()->startOfDay(), $weekEnd]);
            }])
            ->whereIn('student_id', $studentIds)
            ->get();

        // Map: [student_id][Y-m-d] => true if paid
        $paidMap = [];
        foreach ($items as $item) {
            if (!$item->payment || !$item->payment->paid_at) continue;
            $d = Carbon::parse($item->payment->paid_at)->toDateString();
            $paidMap[$item->student_id][$d] = true;
        }

        // Prefetch week's feeding attendance marked as 'fed' to reduce paid days left
        $attendance = \App\Models\FeedingAttendance::whereIn('student_id', $studentIds)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->where('status', 'fed')
            ->get(['student_id', 'date']);
        $fedMap = [];
        foreach ($attendance as $att) {
            $fedMap[$att->student_id][\Carbon\Carbon::parse($att->date)->toDateString()] = true;
        }

        // Build roster map per student for weekdays
        $roster = [];
        foreach ($students as $student) {
            $weekPaid = [];
            foreach ($days as $key => $d) {
                $dateKey = $d->toDateString();
                $weekPaid[$key] = isset($paidMap[$student->id]) && isset($paidMap[$student->id][$dateKey]);
            }
            $todayPaid = $weekPaid[$this->dayKeyForDate($date)] ?? false;
            // Compute days paid left = count of paid days in week minus days already marked 'fed'
            $paidCount = 0; $fedCount = 0;
            foreach ($days as $d) {
                $dk = $d->toDateString();
                if (isset($paidMap[$student->id][$dk])) {
                    $paidCount++;
                    if (isset($fedMap[$student->id][$dk])) {
                        $fedCount++;
                    }
                }
            }
            $daysPaidLeft = max(0, $paidCount - $fedCount);
            if ($status === 'paid' && !$todayPaid) {
                continue; // filtered out
            }
            if ($status === 'not paid' && $todayPaid) {
                continue; // filtered out
            }
            $roster[$student->id] = [
                'student' => $student,
                'week' => $weekPaid,
                'todayPaid' => $todayPaid,
                'daysPaidLeft' => $daysPaidLeft,
            ];
        }

        $paidTodayCount = collect($roster)->filter(fn($r) => $r['todayPaid'])->count();
        $totalCount = count($roster);

        // For class filter dropdown values
        $classes = Student::select('grade')->distinct()->orderBy('grade')->pluck('grade')->filter();

        return view('admin.daily-roster.index', [
            'date' => $date->toDateString(),
            'weekStart' => $weekStart,
            'roster' => $roster,
            'students' => $students,
            'classes' => $classes,
            'selectedClass' => $request->get('class'),
            'q' => $request->get('q'),
            'status' => $status,
            'paidTodayCount' => $paidTodayCount,
            'totalCount' => $totalCount,
        ]);
    }

    private function dayKeyForDate(Carbon $date): string
    {
        switch ($date->dayOfWeekIso) { // 1=Mon..7=Sun
            case 1: return 'M';
            case 2: return 'T';
            case 3: return 'W';
            case 4: return 'T2';
            case 5: return 'F';
            default: return 'M';
        }
    }

    public function export(Request $request)
    {
        $date = Carbon::parse($request->get('date', now()->format('Y-m-d')));
        $weekStart = (clone $date)->startOfWeek(Carbon::MONDAY);
        $days = [
            'M' => (clone $weekStart)->copy(),
            'T' => (clone $weekStart)->copy()->addDays(1),
            'W' => (clone $weekStart)->copy()->addDays(2),
            'T2' => (clone $weekStart)->copy()->addDays(3),
            'F' => (clone $weekStart)->copy()->addDays(4),
        ];

        $query = Student::query();
        if ($q = $request->get('q')) {
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('first_name', 'like', "%$q%")
                    ->orWhere('last_name', 'like', "%$q%")
                    ->orWhere('student_id', 'like', "%$q%");
            });
        }
        if ($grade = $request->get('class')) {
            $query->where('grade', $grade);
        }

        $students = $query->orderBy('first_name')->get();
        $studentIds = $students->pluck('id')->all();
        $weekEnd = (clone $weekStart)->copy()->addDays(4)->endOfDay();

        $items = PaymentItem::with(['payment' => function ($q) use ($weekStart, $weekEnd) {
                $q->where('status', 'completed')
                  ->whereBetween('paid_at', [$weekStart->copy()->startOfDay(), $weekEnd]);
            }])
            ->whereIn('student_id', $studentIds)
            ->get();

        $paidMap = [];
        foreach ($items as $item) {
            if (!$item->payment || !$item->payment->paid_at) continue;
            $d = Carbon::parse($item->payment->paid_at)->toDateString();
            $paidMap[$item->student_id][$d] = true;
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="daily-roster-' . $date->toDateString() . '.csv"',
        ];

        $callback = function () use ($students, $days, $paidMap, $date) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Student Name', 'Student ID', 'Class', 'M', 'T', 'W', 'T', 'F', 'Status']);
            foreach ($students as $student) {
                $row = [];
                $row[] = $student->full_name;
                $row[] = $student->student_id;
                $row[] = $student->grade;
                $weekVals = [];
                $todayPaid = false;
                foreach ($days as $key => $d) {
                    $dateKey = $d->toDateString();
                    $paid = isset($paidMap[$student->id]) && isset($paidMap[$student->id][$dateKey]);
                    $weekVals[$key] = $paid ? 'Paid' : 'Not Paid';
                    if ($d->isSameDay($date)) { $todayPaid = $paid; }
                }
                // Output order M,T,W,T,F
                $row[] = $weekVals['M'] ?? 'Not Paid';
                $row[] = $weekVals['T'] ?? 'Not Paid';
                $row[] = $weekVals['W'] ?? 'Not Paid';
                $row[] = $weekVals['T2'] ?? 'Not Paid';
                $row[] = $weekVals['F'] ?? 'Not Paid';
                $row[] = $todayPaid ? 'Paid' : 'Not Paid';
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, 'daily-roster-' . $date->toDateString() . '.csv', $headers);
    }
}
