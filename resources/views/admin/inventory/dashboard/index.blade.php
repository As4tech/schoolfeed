<x-app-layout>
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium">Inventory Dashboard</h2>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.inventory.items.index') }}" class="px-3 py-2 bg-gray-100 rounded">Manage Items</a>
            <a href="{{ route('admin.inventory.stock-in.create') }}" class="px-3 py-2 bg-green-600 text-white rounded">Stock In</a>
            <a href="{{ route('admin.inventory.stock-out.create') }}" class="px-3 py-2 bg-red-600 text-white rounded">Stock Out</a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded shadow-sm">
                <p class="text-sm text-gray-600">Total Items</p>
                <p class="text-2xl font-semibold">{{ $totalItems }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow-sm">
                <p class="text-sm text-gray-600">Total Quantity</p>
                <p class="text-2xl font-semibold">{{ number_format($totalQuantity, 3) }}</p>
            </div>
            <div class="bg-white p-4 rounded shadow-sm">
                <p class="text-sm text-gray-600">Low Stock</p>
                <p class="text-2xl font-semibold text-red-600">{{ $lowStockCount }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded shadow-sm">
                <div class="p-4 border-b">
                    <h3 class="font-medium">Low Stock Items</h3>
                </div>
                <div class="p-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($lowStockItems as $li)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $li->name }}</td>
                                    <td class="px-6 py-4 text-sm">{{ number_format($li->quantity, 3) }} {{ $li->unit }}</td>
                                    <td class="px-6 py-4 text-sm">{{ number_format($li->min_level, 3) }} {{ $li->unit }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <a href="{{ route('admin.inventory.stock-in.create') }}" class="text-indigo-600 hover:text-indigo-900">Restock</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No low-stock items 🎉</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded shadow-sm">
                <div class="p-4 border-b">
                    <h3 class="font-medium">Recent Movements</h3>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-medium mb-2 text-green-700">Recent Stock In</h4>
                        <ul class="space-y-2 text-sm text-gray-700">
                            @forelse($recentIns as $si)
                                <li class="flex justify-between"><span>{{ $si->date->format('M d') }} • {{ $si->item->name }}</span><span>+{{ number_format($si->quantity,3) }}</span></li>
                            @empty
                                <li class="text-gray-500">No entries</li>
                            @endforelse
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2 text-red-700">Recent Stock Out</h4>
                        <ul class="space-y-2 text-sm text-gray-700">
                            @forelse($recentOuts as $so)
                                <li class="flex justify-between"><span>{{ $so->date->format('M d') }} • {{ $so->item->name }}</span><span>-{{ number_format($so->quantity,3) }}</span></li>
                            @empty
                                <li class="text-gray-500">No entries</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
