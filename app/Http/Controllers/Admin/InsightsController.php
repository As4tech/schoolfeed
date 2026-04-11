<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\School;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsightsController extends Controller
{
    public function schools(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $schoolId = $request->input('school_id');

        $schools = School::orderBy('name')->get(['id','name']);

        $selectedSchool = null;
        $stats = null;

        if ($schoolId) {
            $selectedSchool = $schools->firstWhere('id', (int)$schoolId);
            if ($selectedSchool) {
                $onDate = Carbon::parse($date)->startOfDay();

                // Total students in school
                $totalStudents = Student::where('school_id', $selectedSchool->id)->count();

                // Students with paid/active meal plan on the given date (via student_plan pivot)
                $studentsPaidForMeal = DB::table('student_plan')
                    ->join('students', 'students.id', '=', 'student_plan.student_id')
                    ->where('students.school_id', $selectedSchool->id)
                    ->where('student_plan.status', 'active')
                    ->whereDate('student_plan.start_date', '<=', $onDate)
                    ->whereDate('student_plan.end_date', '>=', $onDate)
                    ->distinct('student_plan.student_id')
                    ->count('student_plan.student_id');

                // Amount received by the school on that date (sum of school_amount for payments with paid_at)
                $amountReceived = Payment::where('school_id', $selectedSchool->id)
                    ->whereNotNull('paid_at')
                    ->whereDate('paid_at', $onDate)
                    ->sum('school_amount');

                // 1% amount from the school
                $onePercent = round($amountReceived * 0.01, 2);

                $stats = [
                    'total_students' => $totalStudents,
                    'students_paid' => $studentsPaidForMeal,
                    'amount_received' => $amountReceived,
                    'one_percent' => $onePercent,
                ];
            }
        }

        return view('admin.insights.schools', compact('date', 'schoolId', 'schools', 'selectedSchool', 'stats'));
    }
}
