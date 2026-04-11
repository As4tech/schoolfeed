<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $classes = $user->hasRole('Super Admin')
            ? SchoolClass::orderBy('name')->get()
            : SchoolClass::where('school_id', $user->school_id)->orderBy('name')->get();

        // Optional quick stats for selected source class
        $fromClassId = $request->get('from_class_id');
        $stats = null;
        if ($fromClassId) {
            $query = Student::query()->where('class_id', $fromClassId);
            if (!$user->hasRole('Super Admin')) {
                $query->where('school_id', $user->school_id);
            }
            $stats = [
                'total' => (clone $query)->count(),
                'enrolled' => (clone $query)->where('status', 'enrolled')->count(),
            ];
        }

        return view('admin.promotions.index', compact('classes', 'stats', 'fromClassId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'from_class_id' => 'required|exists:school_classes,id|different:to_class_id',
            'to_class_id' => 'required|exists:school_classes,id',
            'only_enrolled' => 'nullable|boolean',
        ]);

        $from = SchoolClass::findOrFail($request->from_class_id);
        $to = SchoolClass::findOrFail($request->to_class_id);

        // Scope safety: non-super admins can only promote within their school
        if (!$user->hasRole('Super Admin') && ($from->school_id !== $user->school_id || $to->school_id !== $user->school_id)) {
            abort(403);
        }

        $students = Student::query()
            ->where('class_id', $from->id)
            ->when(!$user->hasRole('Super Admin'), fn($q) => $q->where('school_id', $user->school_id))
            ->when($request->boolean('only_enrolled', true), fn($q) => $q->where('status', 'enrolled'))
            ->get(['id', 'school_id']);

        $updated = 0;
        foreach ($students as $s) {
            // Mirror class name into grade for backward compatibility
            Student::where('id', $s->id)->update([
                'class_id' => $to->id,
                'grade' => $to->name,
            ]);
            $updated++;
        }

        return redirect()->route('admin.promotions.index')
            ->with('success', "Promoted {$updated} student(s) from '{$from->name}' to '{$to->name}'.");
    }
}
