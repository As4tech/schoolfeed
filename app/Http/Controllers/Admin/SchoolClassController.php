<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = SchoolClass::query();
        if (!$user->hasRole('Super Admin')) {
            $query->where('school_id', $user->school_id);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        $classes = $query->orderBy('name')->paginate(10)->withQueryString();
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        SchoolClass::create([
            'school_id' => auth()->user()->school_id,
            'name' => $request->name,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.classes.index', ['school' => $school])->with('success', 'Class created successfully.');
    }

    public function edit(School $school, SchoolClass $class)
    {
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && $class->school_id !== $user->school_id) {
            abort(403);
        }
        return view('admin.classes.edit', compact('class'));
    }

    public function update(Request $request, School $school, SchoolClass $class)
    {
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && $class->school_id !== $user->school_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $class->update([
            'name' => $request->name,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.classes.index', ['school' => $school])->with('success', 'Class updated successfully.');
    }

    public function destroy(School $school, SchoolClass $class)
    {
        $user = auth()->user();
        if (!$user->hasRole('Super Admin') && $class->school_id !== $user->school_id) {
            abort(403);
        }
        $class->delete();
        return redirect()->route('admin.classes.index', ['school' => $school])->with('success', 'Class deleted successfully.');
    }
}
