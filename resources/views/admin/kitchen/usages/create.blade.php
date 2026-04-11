<x-app-layout>
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium">Record Kitchen Usage</h2>
        <a href="{{ route('admin.kitchen.usages.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded">Back</a>
    </div>

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.kitchen.usages.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date *</label>
                            <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                            @error('date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Students Fed *</label>
                            <input type="number" min="0" name="students_fed" value="{{ old('students_fed') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                            @error('students_fed')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Meal Name (uses recipe) *</label>
                        <input name="meal_name" list="meal_names" value="{{ old('meal_name') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                        <datalist id="meal_names">
                            @foreach(($recipes ?? collect())->keys() as $meal)
                                <option value="{{ $meal }}">{{ $meal }}</option>
                            @endforeach
                        </datalist>
                        @error('meal_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        @error('notes')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <p class="text-sm text-gray-600">On save, ingredients will be deducted based on the recipe: quantity_per_student × students_fed.</p>
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.kitchen.usages.index') }}" class="px-4 py-2 text-gray-700">Cancel</a>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
