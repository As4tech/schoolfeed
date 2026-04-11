<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Student Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">{{ $student->full_name }}</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.students.plans.create', ['school' => request()->route('school'), 'student' => $student]) }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Assign Plan
                            </a>
                            <a href="{{ route('admin.students.edit', ['school' => request()->route('school'), 'student' => $student]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Edit
                            </a>
                            <a href="{{ route('admin.students.index', ['school' => request()->route('school')]) }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-700 mb-3">Basic Information</h4>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm text-gray-600">Student ID</p>
                                    <p class="font-medium">{{ $student->student_id }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Grade/Class</p>
                                    <p class="font-medium">{{ $student->grade }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $student->status === 'enrolled' ? 'bg-green-100 text-green-800' : 
                                           ($student->status === 'graduated' ? 'bg-blue-100 text-blue-800' : 
                                           ($student->status === 'withdrawn' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst($student->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Date of Birth</p>
                                    <p class="font-medium">{{ $student->date_of_birth?->format('M d, Y') ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Gender</p>
                                    <p class="font-medium">{{ ucfirst($student->gender) ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-700 mb-3">Parent/Guardian</h4>
                            @if($student->parent)
                                <div class="space-y-2">
                                    <div>
                                        <p class="text-sm text-gray-600">Name</p>
                                        <p class="font-medium">{{ $student->parent->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Email</p>
                                        <p class="font-medium">{{ $student->parent->email ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Phone</p>
                                        <p class="font-medium">{{ $student->parent->phone }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.guardians.show', ['school' => request()->route('school'), 'guardian' => $student->parent]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            View Parent Details →
                                        </a>
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500">No parent/guardian assigned.</p>
                            @endif
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-700 mb-3">Emergency Contact</h4>
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm text-gray-600">Name</p>
                                    <p class="font-medium">{{ $student->emergency_contact_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Phone</p>
                                    <p class="font-medium">{{ $student->emergency_contact_phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-700 mb-3">Allergies</h4>
                            <p class="text-gray-700">{{ $student->allergies ?? 'None recorded' }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-700 mb-3">Medical Notes</h4>
                            <p class="text-gray-700">{{ $student->medical_notes ?? 'None recorded' }}</p>
                        </div>
                    </div>

                    <!-- Feeding Plans Section -->
                    <div class="mt-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Feeding Plans</h4>
                        @if($student->feedingPlans->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Plan</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">End</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($student->feedingPlans as $plan)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $plan->name }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $plan->type_label }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">
                                                    {{ $plan->pivot->start_date ? \Carbon\Carbon::parse($plan->pivot->start_date)->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-500">
                                                    {{ $plan->pivot->end_date ? \Carbon\Carbon::parse($plan->pivot->end_date)->format('M d, Y') : 'N/A' }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $plan->pivot->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                           ($plan->pivot->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                                        {{ ucfirst($plan->pivot->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-500">GH₵{{ number_format($plan->pivot->amount_paid, 2) }}</td>
                                                <td class="px-4 py-2 text-sm font-medium">
                                                    <a href="{{ route('admin.students.plans.edit', ['school' => request()->route('school'), 'student' => $student, 'planId' => $plan->pivot->feeding_plan_id]) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                                    <form action="{{ route('admin.students.plans.destroy', ['school' => request()->route('school'), 'student' => $student, 'planId' => $plan->pivot->feeding_plan_id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Remove</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">No feeding plans assigned yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
