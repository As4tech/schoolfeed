<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\WeeklyMealSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyMealScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $school = $user->school;
        
        // Get current week's schedules
        $weekStartDate = Carbon::now()->startOfWeek();
        $weekEndDate = $weekStartDate->copy()->addDays(4);
        
        $schedules = WeeklyMealSchedule::where('school_id', $school->id)
            ->where('week_start_date', $weekStartDate)
            ->with(['meal'])
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('day_of_week');

        // Get available meals for selection
        $meals = Meal::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.weekly-meal-schedules.index', compact(
            'schedules',
            'meals',
            'weekStartDate',
            'weekEndDate'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $school = $user->school;

        $validated = $request->validate([
            'week_start_date' => 'required|date',
            'meals' => 'required|array',
            'meals.*.meal_id' => 'required|exists:meals,id',
            'meals.*.price' => 'required|numeric|min:0',
        ]);

        $weekStartDate = Carbon::parse($validated['week_start_date']);

        // Clear existing schedules for this week
        WeeklyMealSchedule::where('school_id', $school->id)
            ->where('week_start_date', $weekStartDate)
            ->delete();

        // Create new schedules
        foreach ($validated['meals'] as $dayOfWeek => $mealData) {
            // Skip if no meal is selected
            if (empty($mealData['meal_id'])) {
                continue;
            }
            
            // Verify meal belongs to this school
            $meal = Meal::where('id', $mealData['meal_id'])
                ->where('school_id', $school->id)
                ->first();

            if (!$meal) {
                continue;
            }

            WeeklyMealSchedule::create([
                'school_id' => $school->id,
                'meal_id' => $meal->id,
                'day_of_week' => $dayOfWeek,
                'price' => $mealData['price'],
                'week_start_date' => $weekStartDate,
                'is_active' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Weekly meal schedule updated successfully.'
        ]);
    }

    public function showWeek(Request $request)
    {
        $validated = $request->validate([
            'week_start_date' => 'required|date',
        ]);

        $user = Auth::user();
        $school = $user->school;
        $weekStartDate = Carbon::parse($validated['week_start_date']);

        $schedules = WeeklyMealSchedule::where('school_id', $school->id)
            ->where('week_start_date', $weekStartDate)
            ->with(['meal'])
            ->orderBy('day_of_week')
            ->get()
            ->groupBy('day_of_week');

        return response()->json([
            'schedules' => $schedules,
            'week_start_date' => $weekStartDate->format('Y-m-d'),
        ]);
    }

    public function copyWeek(Request $request)
    {
        $validated = $request->validate([
            'from_week_start_date' => 'required|date',
            'to_week_start_date' => 'required|date|different:from_week_start_date',
        ]);

        $user = Auth::user();
        $school = $user->school;

        $fromWeekStart = Carbon::parse($validated['from_week_start_date']);
        $toWeekStart = Carbon::parse($validated['to_week_start_date']);

        // Clear existing schedules for target week
        WeeklyMealSchedule::where('school_id', $school->id)
            ->where('week_start_date', $toWeekStart)
            ->delete();

        // Copy schedules from source week
        $sourceSchedules = WeeklyMealSchedule::where('school_id', $school->id)
            ->where('week_start_date', $fromWeekStart)
            ->get();

        foreach ($sourceSchedules as $schedule) {
            WeeklyMealSchedule::create([
                'school_id' => $school->id,
                'meal_id' => $schedule->meal_id,
                'day_of_week' => $schedule->day_of_week,
                'price' => $schedule->price,
                'week_start_date' => $toWeekStart,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.weekly-meal-schedules.index')
            ->with('success', 'Weekly meal schedule copied successfully.');
    }
}
