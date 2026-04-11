<x-app-layout>
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium">Inventory</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.inventory.items.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Add Item</a>
            <a href="{{ route('admin.inventory.stock-in.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">Stock In</a>
            <a href="{{ route('admin.inventory.stock-out.create') }}" class="px-4 py-2 bg-red-600 text-white rounded">Stock Out</a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white p-4 rounded shadow-sm">
                <p class="text-sm text-gray-600">Total Items</p>
                <p class="text-2xl font-semibold">{{ $items->total() }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow-sm">
                <p class="text-sm text-gray-600">Low Stock</p>
                <p class="text-2xl font-semibold text-red-600">{{ $lowStock }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow-sm">
                <form method="GET" class="flex items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search item..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">Filter</button>
                </form>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Level</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($items as $item)
                            @php $isLow = (float)$item->quantity <= (float)$item->min_level; @endphp
                            <tr class="{{ $isLow ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->name }}
                                    @if($isLow)
                                        <span class="ml-2 inline-flex text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700">Low</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->unit }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->quantity, 3) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($item->min_level, 3) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.inventory.items.edit', ['school' => request()->route('school'), 'item' => $item]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 inline-flex items-center justify-center w-8 h-8" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.inventory.items.destroy', ['school' => request()->route('school'), 'item' => $item]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 inline-flex items-center justify-center w-8 h-8" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No items. <a href="{{ route('admin.inventory.items.create') }}" class="text-indigo-600">Create one</a>.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $items->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
