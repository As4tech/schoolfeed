<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Feeding Plan Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">{{ $feedingPlan->name }}</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.feeding-plans.edit', ['school' => request()->route('school'), 'feeding_plan' => $feedingPlan]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Edit
                            </a>
                            <a href="{{ route('admin.feeding-plans.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Type</p>
                            <p class="font-medium">{{ $feedingPlan->type_label }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Price</p>
                            <p class="font-medium">{{ $feedingPlan->formatted_price }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Duration</p>
                            <p class="font-medium">{{ $feedingPlan->duration_days }} days</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $feedingPlan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $feedingPlan->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    @if($feedingPlan->description)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">Description</p>
                            <p class="font-medium">{{ $feedingPlan->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Enrolled Students ({{ $feedingPlan->students->count() }})</h3>
                    
                    @if($feedingPlan->students->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent/Guardian</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($feedingPlan->students as $student)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->full_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->parent?->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($student->pivot->start_date)->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($student->pivot->end_date)->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $student->pivot->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                       ($student->pivot->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($student->pivot->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">GH₵{{ number_format($student->pivot->amount_paid, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No students enrolled in this plan yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
