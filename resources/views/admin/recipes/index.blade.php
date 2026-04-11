<x-app-layout>
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium">Meal Recipes</h2>
    </div>

    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-md font-medium mb-3">Add Ingredient to Meal</h3>
                <form action="{{ route('admin.recipes.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    @csrf
                    <div>
                        <label class="block text-sm text-gray-700">Meal Name *</label>
                        <input name="meal_name" value="{{ old('meal_name') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Item *</label>
                        <select name="item_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Select item</option>
                            @foreach($items as $i)
                                <option value="{{ $i->id }}" {{ old('item_id')==$i->id?'selected':'' }}>{{ $i->name }} ({{ $i->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Qty per Student *</label>
                        <input type="number" step="0.0001" min="0.0001" name="quantity_per_student" value="{{ old('quantity_per_student') }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                    </div>
                    <div class="flex md:justify-end">
                        <button class="h-10 px-4 bg-blue-600 text-white rounded self-end">Add</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty/Student</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recipes as $r)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $r->meal_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $r->item->name }} ({{ $r->item->unit }})</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($r->quantity_per_student, 4) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('admin.recipes.destroy', $r) }}" method="POST" onsubmit="return confirm('Remove this ingredient?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-900 inline-flex items-center justify-center w-8 h-8" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">No recipe ingredients defined yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $recipes->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
