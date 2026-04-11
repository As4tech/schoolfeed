<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Children
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($children->isNotEmpty())
                        <!-- Children Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($children as $child)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                                    <div class="p-6">
                                        <!-- Child Avatar and Basic Info -->
                                        <div class="flex items-center mb-4">
                                            <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                                                {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $child->full_name }}</h3>
                                                <p class="text-sm text-gray-500">ID: {{ $child->student_id }}</p>
                                            </div>
                                        </div>

                                        <!-- Child Details -->
                                        <div class="space-y-2 mb-4">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Grade:</span>
                                                <span class="font-medium">{{ $child->grade }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">School:</span>
                                                <span class="font-medium">{{ $child->school->name }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Gender:</span>
                                                <span class="font-medium">{{ $child->gender }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Age:</span>
                                                <span class="font-medium">{{ $child->age ?? 'N/A' }} years</span>
                                            </div>
                                            @if($child->allergies)
                                                <div class="text-sm">
                                                    <span class="text-gray-600">Allergies:</span>
                                                    <span class="font-medium text-red-600">{{ $child->allergies }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Feeding Plan Status -->
                                        <div class="border-t pt-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-sm font-medium text-gray-700">Feeding Plan Status</span>
                                                @if($child->feedingPlans->count() > 0)
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                        No Plan
                                                    </span>
                                                @endif
                                            </div>

                                            @if($child->feedingPlans->count() > 0)
                                                <div class="space-y-1">
                                                    @foreach($child->feedingPlans as $plan)
                                                        <div class="text-xs text-gray-600">
                                                            <span>{{ $plan->name }}</span>
                                                            <span class="float-right">GHS {{ number_format($plan->price, 2) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Actions -->
                                        <div class="mt-4 flex space-x-2">
                                            <a href="{{ route('admin.students.show', ['school' => request()->route('school'), 'student' => $child]) }}" 
                                               class="flex-1 text-center px-3 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                                                View Details
                                            </a>
                                            <a href="{{ route('parent.meals.index', ['school' => request()->route('school'), 'student' => $child]) }}" 
                                               class="flex-1 text-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700 transition-colors">
                                                Select Meals
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No children registered</h3>
                            <p class="mt-1 text-sm text-gray-500">No children have been registered to your account yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
