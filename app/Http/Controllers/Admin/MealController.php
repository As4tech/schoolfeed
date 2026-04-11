<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('Super Admin')) {
            $meals = Meal::with('school')->latest()->paginate(20);
        } else {
            $school = $user->school;
            $meals = Meal::where('school_id', $school->id)->latest()->paginate(20);
        }
        
        return view('admin.meals.index', compact('meals'));
    }

    public function create()
    {
        $user = Auth::user();
        $schools = $user->hasRole('Super Admin') ? School::all() : collect([$user->school]);
        
        return view('admin.meals.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'allergens' => 'nullable|string|max:255',
            'ingredients' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Meal::create($validated);

        return redirect()->route('admin.meals.index')
            ->with('success', 'Meal created successfully.');
    }

    public function show(Meal $meal)
    {
        return view('admin.meals.show', compact('meal'));
    }

    public function edit(Meal $meal)
    {
        $user = Auth::user();
        $schools = $user->hasRole('Super Admin') ? School::all() : collect([$user->school]);
        
        return view('admin.meals.edit', compact('meal', 'schools'));
    }

    public function update(Request $request, Meal $meal)
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'allergens' => 'nullable|string|max:255',
            'ingredients' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $meal->update($validated);

        return redirect()->route('admin.meals.index')
            ->with('success', 'Meal updated successfully.');
    }

    public function destroy(Meal $meal)
    {
        $meal->delete();

        return redirect()->route('admin.meals.index')
            ->with('success', 'Meal deleted successfully.');
    }
}
