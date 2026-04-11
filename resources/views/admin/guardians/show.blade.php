<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Parent/Guardian Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">{{ $guardian->name }}</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.guardians.edit', $guardian) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Edit
                            </a>
                            <a href="{{ route('admin.guardians.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium">{{ $guardian->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="font-medium">{{ $guardian->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Occupation</p>
                            <p class="font-medium">{{ $guardian->occupation ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-600">Address</p>
                            <p class="font-medium">{{ $guardian->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Students ({{ $guardian->students->count() }})</h3>
                    
                    @if($guardian->students->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($guardian->students as $student)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->student_id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->full_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->grade }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $student->status === 'enrolled' ? 'bg-green-100 text-green-800' : 
                                                       ($student->status === 'graduated' ? 'bg-blue-100 text-blue-800' : 
                                                       ($student->status === 'withdrawn' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) }}">
                                                    {{ ucfirst($student->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No students assigned to this parent/guardian.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
