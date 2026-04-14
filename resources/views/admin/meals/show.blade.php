<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Meal Details') }}</h2>
                <p class="text-sm text-gray-600 mt-1">View meal information</p>
            </div>
            <a href="{{ route('admin.meals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Meals
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $meal->name }}</h3>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $meal->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $meal->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="text-right">
                            <a href="{{ route('admin.meals.edit', ['school' => request()->route('school'), 'meal' => $meal]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Meal
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">School</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $meal->school->name ?? 'N/A' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Calories</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $meal->calories ?? '-' }} kcal</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Allergens</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $meal->allergens ?? 'None specified' }}</dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $meal->created_at->format('M d, Y H:i') }}</dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $meal->description ?? 'No description' }}</dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Ingredients</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $meal->ingredients ?? 'No ingredients listed' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
