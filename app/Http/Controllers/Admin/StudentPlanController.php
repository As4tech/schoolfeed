<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedingPlan;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentPlanController extends Controller
{
    public function create(Student $student)
    {
        $feedingPlans = FeedingPlan::where('is_active', true)->orderBy('name')->get();
        return view('admin.student-plans.create', compact('student', 'feedingPlans'));
    }

    public function store(Request $request, Student $student)
    {
        $validated = $request->validate([
            'feeding_plan_id' => 'required|exists:feeding_plans,id',
            'start_date' => 'required|date',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $feedingPlan = FeedingPlan::findOrFail($validated['feeding_plan_id']);
        
        // Calculate end date based on duration
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = $startDate->copy()->addDays($feedingPlan->duration_days);

        // Check if student already has this plan active
        $existing = $student->feedingPlans()
            ->where('feeding_plan_id', $validated['feeding_plan_id'])
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Student already has an active subscription for this plan.');
        }

        $student->feedingPlans()->attach($feedingPlan->id, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'amount_paid' => $validated['amount_paid'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Feeding plan assigned successfully.');
    }

    public function edit(Student $student, $planId)
    {
        $studentPlan = $student->feedingPlans()->where('student_plan.id', $planId)->firstOrFail();
        $feedingPlans = FeedingPlan::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.student-plans.edit', compact('student', 'studentPlan', 'feedingPlans'));
    }

    public function update(Request $request, Student $student, $planId)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,completed,cancelled',
            'amount_paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $student->feedingPlans()->updateExistingPivot($planId, [
            'status' => $validated['status'],
            'amount_paid' => $validated['amount_paid'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Plan assignment updated successfully.');
    }

    public function destroy(Student $student, $planId)
    {
        $student->feedingPlans()->detach($planId);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Plan assignment removed successfully.');
    }
}
