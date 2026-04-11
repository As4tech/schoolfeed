<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\MealSelection;
use App\Models\Student;
use App\Models\WeeklyMealSchedule;
use App\Models\Payment;
use App\Models\PaymentItem;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentMealController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guardian = $user->guardian;
        
        if (!$guardian) {
            abort(403, 'Guardian profile not found');
        }

        $children = $guardian->students()->where('status', 'enrolled')->get();
        
        // Get current week's start date (Monday)
        $weekStartDate = Carbon::now()->startOfWeek();
        
        // Get weekly meal schedules for the current week
        $weeklySchedules = WeeklyMealSchedule::where('school_id', $children->first()->school_id ?? null)
            ->forWeek($weekStartDate)
            ->active()
            ->with(['meal'])
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('day_of_week');

        // Get existing meal selections for this week
        $existingSelections = MealSelection::whereIn('student_id', $children->pluck('id'))
            ->forWeek($weekStartDate)
            ->with(['weeklyMealSchedule.meal'])
            ->get()
            ->keyBy(function ($selection) {
                return $selection->student_id . '_' . $selection->weeklyMealSchedule->day_of_week;
            });

        return view('parent.meals.index', compact(
            'children',
            'weeklySchedules',
            'existingSelections',
            'weekStartDate'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $guardian = $user->guardian;
        
        if (!$guardian) {
            return response()->json(['error' => 'Guardian profile not found'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'selections' => 'required|array',
            'selections.*' => 'exists:weekly_meal_schedules,id',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        
        // Verify the student belongs to the current guardian
        if ($student->parent_id !== $guardian->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $weekStartDate = Carbon::now()->startOfWeek();
        
        // Clear existing selections for this student and week
        MealSelection::where('student_id', $student->id)
            ->forWeek($weekStartDate)
            ->delete();

        // Create new selections
        $selections = [];
        $totalAmount = 0;

        foreach ($validated['selections'] as $scheduleId) {
            $schedule = WeeklyMealSchedule::findOrFail($scheduleId);
            
            // Calculate the meal date based on day of week
            $mealDate = $weekStartDate->copy()->addDays([
                'monday' => 0,
                'tuesday' => 1,
                'wednesday' => 2,
                'thursday' => 3,
                'friday' => 4,
            ][$schedule->day_of_week]);

            $selection = MealSelection::create([
                'student_id' => $student->id,
                'weekly_meal_schedule_id' => $schedule->id,
                'parent_id' => $guardian->id,
                'meal_date' => $mealDate,
                'price' => $schedule->price,
                'status' => 'selected',
            ]);

            $selections[] = $selection;
            $totalAmount += $schedule->price;
        }

        return response()->json([
            'success' => true,
            'selections' => $selections,
            'total_amount' => $totalAmount,
            'message' => 'Meals selected successfully'
        ]);
    }

    public function payment(Request $request)
    {
        $user = Auth::user();
        $guardian = $user->guardian;
        
        if (!$guardian) {
            return response()->json(['error' => 'Guardian profile not found'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_method' => 'required|string|in:mtn_momo,airtel_money,vodafone_cash',
            'phone_number' => 'required|string|max:15',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        
        // Verify the student belongs to the current guardian
        if ($student->parent_id !== $guardian->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $weekStartDate = Carbon::now()->startOfWeek();
        
        // Get selected meals for this student and week
        $mealSelections = MealSelection::where('student_id', $student->id)
            ->forWeek($weekStartDate)
            ->selected()
            ->with(['weeklyMealSchedule.meal'])
            ->get();

        if ($mealSelections->isEmpty()) {
            return response()->json(['error' => 'No meals selected for payment'], 400);
        }

        $totalAmount = $mealSelections->sum('price');
        $platformFee = round($totalAmount * 0.01, 2); // 1% platform fee
        $finalAmount = $totalAmount + $platformFee;

        // Create pending payment record (Paystack will complete it)
        $reference = 'MEAL_' . uniqid();
        $payment = Payment::create([
            'school_id' => $student->school_id,
            'guardian_id' => $guardian->id,
            'reference' => $reference,
            'total_amount' => $finalAmount,
            'platform_fee' => $platformFee,
            'school_amount' => $totalAmount,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'notes' => 'Meal payment for ' . $student->full_name . ' - Week of ' . $weekStartDate->format('M d'),
        ]);

        // Create payment items
        foreach ($mealSelections as $selection) {
            PaymentItem::create([
                'payment_id' => $payment->id,
                'student_id' => $student->id,
                'description' => $selection->weeklyMealSchedule->meal->name . ' - ' . $selection->meal_date->format('D, M d'),
                'amount' => $selection->price,
                'notes' => 'Meal for ' . $selection->meal_date->format('l'),
            ]);
        }

        // Link selections to the pending payment
        MealSelection::where('student_id', $student->id)
            ->forWeek($weekStartDate)
            ->selected()
            ->update(['payment_id' => $payment->id]);

        // Initialize Paystack and return authorization URL to frontend
        $school = $student->school;
        $paystackData = [
            'email' => $guardian->email ?? ($guardian->user->email ?? 'parent@example.com'),
            'amount' => $finalAmount,
            'platform_fee' => $platformFee,
            'reference' => $reference,
            'callback_url' => route('payment.callback'),
            'payment_id' => $payment->id,
            'guardian_id' => $guardian->id,
            'school_id' => $school->id,
            'school_name' => $school->name,
            'students' => $mealSelections->map(function($sel){
                return [
                    'id' => $sel->student_id,
                    'name' => $sel->weeklyMealSchedule->meal->name,
                    'amount' => $sel->price,
                ];
            })->toArray(),
            'subaccount_code' => $school->paystack_subaccount_code,
        ];

        /** @var PaymentService $paymentService */
        $paymentService = app(PaymentService::class);
        $result = $paymentService->initialize($paystackData);

        if ($result['success'] ?? false) {
            return response()->json([
                'success' => true,
                'authorization_url' => $result['authorization_url'],
                'reference' => $reference,
            ]);
        }

        // On failure, mark as failed and return error
        $payment->update(['status' => 'failed']);
        return response()->json([
            'success' => false,
            'error' => $result['message'] ?? 'Failed to initialize payment with Paystack.'
        ], 400);
    }

    public function getWeekMeals(Request $request)
    {
        $validated = $request->validate([
            'week_start_date' => 'required|date',
            'student_id' => 'required|exists:students,id',
        ]);

        $user = Auth::user();
        $guardian = $user->guardian;
        
        $student = Student::findOrFail($validated['student_id']);
        
        // Verify the student belongs to the current guardian
        if ($student->parent_id !== $guardian->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $weekStartDate = Carbon::parse($validated['week_start_date']);
        
        $weeklySchedules = WeeklyMealSchedule::where('school_id', $student->school_id)
            ->forWeek($weekStartDate)
            ->active()
            ->with(['meal'])
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('day_of_week');

        $existingSelections = MealSelection::where('student_id', $student->id)
            ->forWeek($weekStartDate)
            ->with(['weeklyMealSchedule.meal'])
            ->get()
            ->keyBy(function ($selection) {
                return $selection->student_id . '_' . $selection->weeklyMealSchedule->day_of_week;
            });

        return response()->json([
            'weekly_schedules' => $weeklySchedules,
            'existing_selections' => $existingSelections,
            'week_start_date' => $weekStartDate->format('Y-m-d'),
        ]);
    }
}
