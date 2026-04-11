<x-app-layout>
    <div class="flex justify-between items-center mb-6 bg-white p-4 rounded-lg shadow-sm">
        <h2 class="text-lg font-medium">Kitchen Usage</h2>
        <a href="{{ route('admin.kitchen.usages.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Record Usage</a>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <form method="GET" class="flex items-end gap-3 mb-4">
                    <div>
                        <label class="block text-sm text-gray-700">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button class="px-3 py-2 bg-gray-100 rounded hover:bg-gray-200">Filter</button>
                </form>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('success') }}</div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students Fed</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items Used</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($usages as $u)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $u->date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $u->students_fed }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        @foreach($u->items as $ui)
                                            <div>- {{ $ui->item->name }}: {{ number_format($ui->quantity,3) }} {{ $ui->item->unit }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $u->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No usage recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $usages->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
