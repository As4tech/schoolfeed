<x-app-layout>
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium">Monthly Usage</h2>
        <a href="{{ route('admin.inventory.dashboard') }}" class="px-3 py-2 bg-gray-100 rounded">Back</a>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded shadow-sm mb-6">
            <div class="p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-sm text-gray-700">Year</label>
                        <input type="number" name="year" value="{{ request('year', $year) }}" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-3 flex md:justify-end">
                        <button class="h-10 px-4 bg-gray-100 rounded self-end">Apply</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded shadow-sm">
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($rows as $r)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $r->ym }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $r->item_name }} ({{ $r->unit }})</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($r->total_qty, 3) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">No data for the selected year.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
