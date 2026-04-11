<x-app-layout>
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium">Edit Inventory Item</h2>
        <a href="{{ route('admin.inventory.items.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded">Back</a>
    </div>

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.inventory.items.update', $item) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name *</label>
                        <input name="name" value="{{ old('name', $item->name) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                        @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit *</label>
                        <select name="unit" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="kg" {{ old('unit', $item->unit)==='kg'?'selected':'' }}>kg</option>
                            <option value="liters" {{ old('unit', $item->unit)==='liters'?'selected':'' }}>liters</option>
                            <option value="pieces" {{ old('unit', $item->unit)==='pieces'?'selected':'' }}>pieces</option>
                        </select>
                        @error('unit')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Minimum Level</label>
                        <input type="number" step="0.001" min="0" name="min_level" value="{{ old('min_level', $item->min_level) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('min_level')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.inventory.items.index') }}" class="px-4 py-2 text-gray-700">Cancel</a>
                        <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
