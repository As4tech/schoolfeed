<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedingPlan;
use App\Models\School;
use Illuminate\Http\Request;

class FeedingPlanController extends Controller
{
    public function index()
    {
        $feedingPlans = FeedingPlan::withCount('students')->paginate(10);
        return view('admin.feeding-plans.index', compact('feedingPlans'));
    }

    public function create()
    {
        return view('admin.feeding-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:daily,weekly,termly',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['school_id'] = auth()->user()->school_id;
        $validated['is_active'] = $request->boolean('is_active', true);

        FeedingPlan::create($validated);

        return redirect()->route('admin.feeding-plans.index')
            ->with('success', 'Feeding plan created successfully.');
    }

    public function show(School $school, FeedingPlan $feedingPlan)
    {
        $feedingPlan->load(['students' => function($query) {
            $query->with('parent')->latest('student_plan.created_at');
        }]);
        return view('admin.feeding-plans.show', compact('feedingPlan'));
    }

    public function edit(School $school, FeedingPlan $feedingPlan)
    {
        return view('admin.feeding-plans.edit', compact('feedingPlan'));
    }

    public function update(Request $request, School $school, FeedingPlan $feedingPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:daily,weekly,termly',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $feedingPlan->update($validated);

        return redirect()->route('admin.feeding-plans.index')
            ->with('success', 'Feeding plan updated successfully.');
    }

    public function destroy(School $school, FeedingPlan $feedingPlan)
    {
        $feedingPlan->delete();

        return redirect()->route('admin.feeding-plans.index')
            ->with('success', 'Feeding plan deleted successfully.');
    }
}
