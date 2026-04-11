<?php

namespace Database\Seeders;

use App\Models\Guardian;
use App\Models\MealSelection;
use App\Models\Student;
use App\Models\WeeklyMealSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MealSelectionSeeder extends Seeder
{
    public function run(): void
    {
        // Get students and guardians
        $students = Student::with('parent')->get();
        $guardians = Guardian::all();

        if ($students->isEmpty()) {
            $this->command->error('No students found. Please run StudentSeeder first.');
            return;
        }

        // Get weekly meal schedules for current week
        $weekStartDate = Carbon::now()->startOfWeek();
        $schedules = WeeklyMealSchedule::where('week_start_date', $weekStartDate)
            ->with(['meal'])
            ->get();

        if ($schedules->isEmpty()) {
            $this->command->error('No weekly meal schedules found. Please run WeeklyMealScheduleSeeder first.');
            return;
        }

        // Create meal selections for students
        foreach ($students as $student) {
            // Randomly select 3-5 meals for the week
            $selectedSchedules = $schedules->random(rand(3, 5));
            
            foreach ($selectedSchedules as $schedule) {
                // Calculate the meal date based on day of week
                $mealDate = $weekStartDate->copy()->addDays([
                    'monday' => 0,
                    'tuesday' => 1,
                    'wednesday' => 2,
                    'thursday' => 3,
                    'friday' => 4,
                ][$schedule->day_of_week]);

                // Randomly determine if this selection is paid or just selected
                $status = rand(0, 1) ? 'paid' : 'selected';
                
                MealSelection::firstOrCreate([
                    'student_id' => $student->id,
                    'weekly_meal_schedule_id' => $schedule->id,
                    'meal_date' => $mealDate,
                ], [
                    'parent_id' => $student->parent_id,
                    'price' => $schedule->price,
                    'status' => $status,
                    'notes' => $status === 'paid' ? 'Payment completed via mobile money' : 'Awaiting payment',
                ]);
            }
        }

        // Create some meal selections for next week as well
        $nextWeekStartDate = Carbon::now()->startOfWeek()->addWeek();
        $nextWeekSchedules = WeeklyMealSchedule::where('week_start_date', $nextWeekStartDate)
            ->with(['meal'])
            ->get();

        foreach ($students->take(2) as $student) { // Only for first 2 students
            $selectedSchedules = $nextWeekSchedules->random(rand(2, 4));
            
            foreach ($selectedSchedules as $schedule) {
                $mealDate = $nextWeekStartDate->copy()->addDays([
                    'monday' => 0,
                    'tuesday' => 1,
                    'wednesday' => 2,
                    'thursday' => 3,
                    'friday' => 4,
                ][$schedule->day_of_week]);

                MealSelection::firstOrCreate([
                    'student_id' => $student->id,
                    'weekly_meal_schedule_id' => $schedule->id,
                    'meal_date' => $mealDate,
                ], [
                    'parent_id' => $student->parent_id,
                    'price' => $schedule->price,
                    'status' => 'selected', // All next week selections are unpaid
                    'notes' => 'Pre-selected for next week',
                ]);
            }
        }

        $this->command->info('Meal selections created successfully!');
    }
}
