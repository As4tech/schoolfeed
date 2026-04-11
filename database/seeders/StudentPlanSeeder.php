<?php

namespace Database\Seeders;

use App\Models\FeedingPlan;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentPlanSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::with('school')->get();
        $feedingPlans = FeedingPlan::all();

        foreach ($students as $student) {
            // Get feeding plans for the student's school
            $schoolPlans = $feedingPlans->where('school_id', $student->school_id);
            
            if ($schoolPlans->isEmpty()) {
                continue;
            }

            // Assign 1-2 feeding plans per student
            $numPlans = rand(1, 2);
            $selectedPlans = $schoolPlans->random(min($numPlans, $schoolPlans->count()));

            foreach ($selectedPlans as $plan) {
                $startDate = Carbon::now()->subDays(rand(0, 30));
                $endDate = $startDate->copy()->addDays($plan->duration_days);
                
                // Randomly determine if plan is active or completed
                $status = rand(0, 1) ? 'active' : 'completed';
                if ($status === 'completed') {
                    $endDate = Carbon::now()->subDays(rand(1, 10));
                }

                $student->feedingPlans()->attach($plan->id, [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status,
                    'amount_paid' => $status === 'active' ? $plan->price : $plan->price * 0.8, // Partial payment for completed
                    'notes' => $status === 'active' ? 'Regular payment' : 'Plan completed, renewal required',
                ]);
            }
        }

        $this->command->info('Student feeding plans assigned successfully!');
    }
}
