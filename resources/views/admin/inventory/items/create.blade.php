<x-app-layout>
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium">Add Inventory Item</h2>
        <a href="{{ route('admin.inventory.items.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded">Back</a>
    </div>

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.inventory.items.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name *</label>
                        <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                        @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit *</label>
                        <select name="unit" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select unit</option>
                            <option value="kg" {{ old('unit')==='kg'?'selected':'' }}>kg</option>
                            <option value="liters" {{ old('unit')==='liters'?'selected':'' }}>liters</option>
                            <option value="pieces" {{ old('unit')==='pieces'?'selected':'' }}>pieces</option>
                        </select>
                        @error('unit')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Starting Quantity</label>
                            <input type="number" step="0.001" min="0" name="quantity" value="{{ old('quantity', '0') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('quantity')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Minimum Level</label>
                            <input type="number" step="0.001" min="0" name="min_level" value="{{ old('min_level', '0') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                            @error('min_level')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.inventory.items.index') }}" class="px-4 py-2 text-gray-700">Cancel</a>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
