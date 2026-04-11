<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daily Roster') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" value="{{ $date }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name/Student ID</label>
                        <input type="text" name="q" value="{{ $q }}" placeholder="Search by name or student ID" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                        <select name="class" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            @foreach($classes as $grade)
                                <option value="{{ $grade }}" {{ $selectedClass == $grade ? 'selected' : '' }}>{{ $grade }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="not paid" {{ $status === 'not paid' ? 'selected' : '' }}>Not Paid</option>
                        </select>
                    </div>
                    <div class="md:col-span-5 flex gap-2 md:justify-end">
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Apply</button>
                        <a href="{{ route('admin.daily-roster.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Reset</a>
                        <a href="{{ route('admin.daily-roster.export', request()->all()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Export CSV</a>
                    </div>
                </form>
            </div>

            <!-- Counters (single row, horizontally scrollable on small screens) -->
            <div class="overflow-x-auto mb-6">
                <div class="inline-flex gap-4 min-w-full">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 min-w-[220px]">
                        <p class="text-sm text-gray-600">Paid Today</p>
                        <p class="text-2xl font-bold text-green-600">{{ $paidTodayCount }}</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4 min-w-[220px]">
                        <p class="text-sm text-gray-600">Total in List</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCount }}</p>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Paid Left</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($students as $student)
                                    @php
                                        $row = $roster[$student->id] ?? null;
                                        $week = $row['week'] ?? [];
                                        $labels = ['M','T','W','T','F'];
                                        $keys = ['M','T','W','T2','F'];
                                        $statusPaid = $row['todayPaid'] ?? false;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->full_name }} <span class="text-xs text-gray-400 block">{{ $student->student_id }}</span></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->grade }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex gap-1">
                                                @foreach($keys as $i => $k)
                                                    @php $paid = $week[$k] ?? false; @endphp
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold {{ $paid ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}" title="{{ $labels[$i] }}">{{ $labels[$i] }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $row['daysPaidLeft'] ?? 0 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($statusPaid)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Not Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No students found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
