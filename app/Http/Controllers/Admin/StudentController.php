<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Start with base query
        $query = Student::with('parent');
        
        // Apply school scope for non-super admins
        if (!$user->hasRole('Super Admin')) {
            $query->where('school_id', $user->school_id);
        }

        // Search by name or student ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Filter by grade
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by guardian
        if ($request->filled('guardian_id')) {
            $query->where('parent_id', $request->guardian_id);
        }

        $students = $query->paginate(10)->withQueryString();
        
        // Get filter options (scoped to school for non-super admins)
        if ($user->hasRole('Super Admin')) {
            $grades = Student::distinct()->pluck('grade')->sort();
            $guardians = Guardian::orderBy('name')->get();
        } else {
            $grades = Student::where('school_id', $user->school_id)->distinct()->pluck('grade')->sort();
            $guardians = Guardian::where('school_id', $user->school_id)->orderBy('name')->get();
        }

        return view('admin.students.index', compact('students', 'grades', 'guardians'));
    }

    public function create()
    {
        $user = auth()->user();
        
        // Get guardians scoped to school for non-super admins
        if ($user->hasRole('Super Admin')) {
            $guardians = Guardian::orderBy('name')->get();
        } else {
            $guardians = Guardian::where('school_id', $user->school_id)->orderBy('name')->get();
        }
        // Get classes scoped to school
        if ($user->hasRole('Super Admin')) {
            $classes = SchoolClass::orderBy('name')->get();
        } else {
            $classes = SchoolClass::where('school_id', $user->school_id)->orderBy('name')->get();
        }

        return view('admin.students.create', compact('guardians', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'student_id' => 'required|string|unique:students|max:50',
            'class_id' => 'required|exists:school_classes,id',
            'grade' => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medical_notes' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            // Guardian fields
            'guardian_option' => 'required|in:existing,new',
            'parent_id' => 'required_if:guardian_option,existing|nullable|exists:guardians,id',
            'guardian_name' => 'required_if:guardian_option,new|nullable|string|max:255',
            'guardian_phone' => 'required_if:guardian_option,new|nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_address' => 'nullable|string|max:255',
            'guardian_occupation' => 'nullable|string|max:255',
        ]);

        // Create new guardian if option is 'new'
        $parentId = null;
        if ($validated['guardian_option'] === 'new' && !empty($validated['guardian_name'])) {
            $guardian = Guardian::create([
                'school_id' => auth()->user()->school_id,
                'name' => $validated['guardian_name'],
                'phone' => $validated['guardian_phone'],
                'email' => $validated['guardian_email'] ?? null,
                'address' => $validated['guardian_address'] ?? null,
                'occupation' => $validated['guardian_occupation'] ?? null,
            ]);
            $parentId = $guardian->id;
        } else {
            $parentId = $validated['parent_id'] ?? null;
        }

        // Resolve class and create student
        $schoolClass = SchoolClass::findOrFail($validated['class_id']);
        Student::create([
            'school_id' => auth()->user()->school_id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'student_id' => $validated['student_id'],
            'class_id' => $schoolClass->id,
            // Backward-compat: copy class name into grade field for existing lists
            'grade' => $schoolClass->name,
            'parent_id' => $parentId,
            'status' => 'enrolled',
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'medical_notes' => $validated['medical_notes'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show(School $school, Student $student)
    {
        $student->load('parent');
        return view('admin.students.show', compact('student'));
    }

    public function edit(School $school, Student $student)
    {
        $user = auth()->user();
        
        // Check authorization - only allow editing if user is super admin or student belongs to user's school
        if (!$user->hasRole('Super Admin') && $student->school_id !== $user->school_id) {
            abort(403, 'Unauthorized');
        }
        
        // Get guardians scoped to school for non-super admins
        if ($user->hasRole('Super Admin')) {
            $guardians = Guardian::orderBy('name')->get();
        } else {
            $guardians = Guardian::where('school_id', $user->school_id)->orderBy('name')->get();
        }
        // Get classes scoped to school
        if ($user->hasRole('Super Admin')) {
            $classes = SchoolClass::orderBy('name')->get();
        } else {
            $classes = SchoolClass::where('school_id', $user->school_id)->orderBy('name')->get();
        }

        return view('admin.students.edit', compact('student', 'guardians', 'classes'));
    }

    public function update(Request $request, School $school, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'student_id' => 'required|string|max:50|unique:students,student_id,' . $student->id,
            'class_id' => 'required|exists:school_classes,id',
            'grade' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:guardians,id',
            'status' => 'required|in:enrolled,graduated,withdrawn,suspended',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|max:10',
            'allergies' => 'nullable|string',
            'medical_notes' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $schoolClass = SchoolClass::findOrFail($validated['class_id']);
        $student->update(array_merge($validated, [
            'class_id' => $schoolClass->id,
            'grade' => $schoolClass->name,
        ]));

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function showImportForm()
    {
        return view('admin.students.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        
        if (count($data) < 2) {
            return redirect()->back()->with('error', 'CSV file is empty or invalid.');
        }

        // Get headers
        $headers = array_map('strtolower', array_map('trim', $data[0]));
        $rows = array_slice($data, 1);
        
        $imported = 0;
        $errors = [];
        $rowNumber = 1;

        foreach ($rows as $row) {
            $rowNumber++;
            if (count($row) < count($headers)) {
                $errors[] = "Row {$rowNumber}: Invalid column count";
                continue;
            }

            $rowData = array_combine($headers, $row);
            
            // Required fields
            if (empty($rowData['first_name']) || empty($rowData['last_name']) || empty($rowData['student_id']) || empty($rowData['grade'])) {
                $errors[] = "Row {$rowNumber}: Missing required fields (first_name, last_name, student_id, grade)";
                continue;
            }

            // Check for duplicate student_id
            if (Student::where('student_id', $rowData['student_id'])->exists()) {
                $errors[] = "Row {$rowNumber}: Student ID '{$rowData['student_id']}' already exists";
                continue;
            }

            // Create or find guardian
            $parentId = null;
            if (!empty($rowData['guardian_phone'])) {
                $guardian = Guardian::firstOrCreate(
                    ['phone' => trim($rowData['guardian_phone']), 'school_id' => auth()->user()->school_id],
                    [
                        'school_id' => auth()->user()->school_id,
                        'name' => trim($rowData['guardian_name'] ?? 'Unknown'),
                        'email' => !empty($rowData['guardian_email']) ? trim($rowData['guardian_email']) : null,
                        'address' => !empty($rowData['guardian_address']) ? trim($rowData['guardian_address']) : null,
                        'occupation' => !empty($rowData['guardian_occupation']) ? trim($rowData['guardian_occupation']) : null,
                    ]
                );
                $parentId = $guardian->id;
            }

            // Create student
            Student::create([
                'school_id' => auth()->user()->school_id,
                'first_name' => trim($rowData['first_name']),
                'last_name' => trim($rowData['last_name']),
                'student_id' => trim($rowData['student_id']),
                'grade' => trim($rowData['grade']),
                'parent_id' => $parentId,
                'status' => 'enrolled',
                'date_of_birth' => !empty($rowData['date_of_birth']) ? $rowData['date_of_birth'] : null,
                'gender' => !empty($rowData['gender']) ? trim($rowData['gender']) : null,
                'allergies' => !empty($rowData['allergies']) ? trim($rowData['allergies']) : null,
                'emergency_contact_name' => !empty($rowData['emergency_contact_name']) ? trim($rowData['emergency_contact_name']) : null,
                'emergency_contact_phone' => !empty($rowData['emergency_contact_phone']) ? trim($rowData['emergency_contact_phone']) : null,
            ]);

            $imported++;
        }

        $message = "Imported {$imported} students successfully.";
        if (!empty($errors)) {
            $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " (and " . (count($errors) - 5) . " more)";
            }
            return redirect()->route('admin.students.index')->with('warning', $message);
        }

        return redirect()->route('admin.students.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_import_template.csv"',
        ];

        $columns = ['first_name', 'last_name', 'student_id', 'grade', 'date_of_birth', 'gender', 'allergies', 'emergency_contact_name', 'emergency_contact_phone', 'guardian_name', 'guardian_phone', 'guardian_email', 'guardian_address', 'guardian_occupation'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['John', 'Doe', 'STD001', 'Grade 5', '2015-03-15', 'male', 'None', 'Jane Doe', '1234567890', 'Jane Doe', '9876543210', 'jane@example.com', '123 Main St', 'Teacher']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
