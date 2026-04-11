<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daily Feeding Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Eligible Today</p>
                    <p class="text-2xl font-bold">{{ $totalEligible }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-green-600">Fed</p>
                    <p class="text-2xl font-bold text-green-700">{{ $fedCount }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-red-600">Not Fed</p>
                    <p class="text-2xl font-bold text-red-700">{{ $notFedCount }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Absent</p>
                    <p class="text-2xl font-bold">{{ $absentCount }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-yellow-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $pendingCount }}</p>
                </div>
            </div>

            <!-- Filters: Date and Class -->
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" id="date" name="date" value="{{ $date }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="class" class="block text-sm font-medium text-gray-700">Class</label>
                        <select id="class" name="class" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            @foreach($classes as $g)
                                <option value="{{ $g }}" {{ ($class ?? '') === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 flex gap-2 md:justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Apply</button>
                        <a href="{{ route('admin.feeding-attendance.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Reset</a>
                        <a href="{{ route('admin.feeding-attendance.report') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">View Report</a>
                    </div>
                </form>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Students Eligible for Feeding</h3>
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($students->isEmpty())
                        <p class="text-gray-500 text-center py-8">No students with active feeding plans for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}.</p>
                    @else
                        <div class="overflow-x-auto">
                            <!-- Bulk actions -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300">
                                        <span>Select all</span>
                                    </label>
                                    <select id="bulk-status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="fed">Mark as Fed</option>
                                        <option value="not_fed">Mark as Not Fed</option>
                                        <option value="absent">Mark as Absent</option>
                                    </select>
                                    <button id="apply-bulk" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Apply to selected</button>
                                    <span id="selected-count" class="text-xs text-gray-600"></span>
                                </div>
                                @if(!empty($class))
                                    <div class="text-xs text-gray-600">Tip: All rows are preselected for class filter. Choose an action and click Apply.</div>
                                @endif
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-3">
                                            <!-- checkbox column header -->
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade/Class</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($students as $student)
                                        @php
                                            $attendance = $student->attendance;
                                            $currentStatus = $attendance?->status ?? 'pending';
                                        @endphp
                                        <tr data-student-id="{{ $student->id }}">
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                <input type="checkbox" class="row-check rounded border-gray-300" data-student="{{ $student->id }}">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->grade }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $student->feedingPlans->first()?->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="flex justify-center gap-2">
                                                    <button type="button" 
                                                        data-status="fed"
                                                        data-student="{{ $student->id }}"
                                                        class="attendance-btn px-3 py-1 rounded text-sm font-medium transition-colors {{ $currentStatus === 'fed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-green-100' }}">
                                                        Fed
                                                    </button>
                                                    <button type="button"
                                                        data-status="not_fed"
                                                        data-student="{{ $student->id }}"
                                                        class="attendance-btn px-3 py-1 rounded text-sm font-medium transition-colors {{ $currentStatus === 'not_fed' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-red-100' }}">
                                                        Not Fed
                                                    </button>
                                                    <button type="button"
                                                        data-status="absent"
                                                        data-student="{{ $student->id }}"
                                                        class="attendance-btn px-3 py-1 rounded text-sm font-medium transition-colors {{ $currentStatus === 'absent' ? 'bg-gray-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                                        Absent
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <input type="text" 
                                                    data-student="{{ $student->id }}"
                                                    class="notes-input w-32 px-2 py-1 text-sm border rounded focus:ring-indigo-500 focus:border-indigo-500"
                                                    placeholder="Add notes..."
                                                    value="{{ $attendance?->notes ?? '' }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        const date = '{{ $date }}';
        const csrfToken = '{{ csrf_token() }}';
        const hasClassFilter = '{{ $class ?? '' }}' !== '';
        
        document.querySelectorAll('.attendance-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.dataset.student;
                const status = this.dataset.status;
                const notesInput = document.querySelector(`.notes-input[data-student="${studentId}"]`);
                const notes = notesInput ? notesInput.value : '';
                
                // Update UI immediately
                updateButtonStyles(studentId, status);
                
                // Send to server
                fetch('{{ route('admin.feeding-attendance.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        student_id: studentId,
                        date: date,
                        status: status,
                        notes: notes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Failed to save attendance');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving attendance');
                });
            });
        });
        
        // Bulk selection helpers
        const selectAll = document.getElementById('select-all');
        const rowChecks = Array.from(document.querySelectorAll('.row-check'));
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowChecks.forEach(ch => ch.checked = this.checked);
                updateSelectedCount();
            });
        }

        // Preselect all when a class filter is applied (default intent: Fed)
        if (hasClassFilter) {
            rowChecks.forEach(ch => ch.checked = true);
            const bulkSel = document.getElementById('bulk-status');
            if (bulkSel) bulkSel.value = 'fed';
            updateSelectedCount();
            // Ask to auto-apply 'Fed' for all preselected students
            setTimeout(() => {
                if (confirm('Mark all selected students as Fed for ' + date + '?')) {
                    applyBulk('fed');
                }
            }, 0);
        }

        // Apply bulk
        const applyBulkBtn = document.getElementById('apply-bulk');
        if (applyBulkBtn) {
            applyBulkBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const status = document.getElementById('bulk-status').value;
                applyBulk(status);
            });
        }
        
        // Keep selected count updated
        rowChecks.forEach(ch => ch.addEventListener('change', updateSelectedCount));
        updateSelectedCount();

        function updateSelectedCount() {
            const count = rowChecks.filter(ch => ch.checked).length;
            const el = document.getElementById('selected-count');
            if (el) el.textContent = count > 0 ? (count + ' selected') : '';
        }

        function applyBulk(status) {
            const selected = rowChecks.filter(ch => ch.checked).map(ch => ch.dataset.student);
            if (selected.length === 0) {
                alert('Select at least one student.');
                return;
            }
            const payload = {
                date: date,
                attendance: selected.map(id => ({ student_id: id, status: status }))
            };
            fetch('{{ route('admin.feeding-attendance.bulk') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Reflect UI state
                    selected.forEach(id => updateButtonStyles(id, status));
                } else {
                    alert('Failed to save attendance');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error saving attendance');
            });
        }
        
        function updateButtonStyles(studentId, status) {
            const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
            const buttons = row.querySelectorAll('.attendance-btn');
            
            buttons.forEach(btn => {
                const btnStatus = btn.dataset.status;
                btn.className = 'attendance-btn px-3 py-1 rounded text-sm font-medium transition-colors ';
                
                if (btnStatus === status) {
                    if (status === 'fed') btn.classList.add('bg-green-600', 'text-white');
                    else if (status === 'not_fed') btn.classList.add('bg-red-600', 'text-white');
                    else if (status === 'absent') btn.classList.add('bg-gray-600', 'text-white');
                } else {
                    btn.classList.add('bg-gray-200', 'text-gray-700');
                    if (btnStatus === 'fed') btn.classList.add('hover:bg-green-100');
                    else if (btnStatus === 'not_fed') btn.classList.add('hover:bg-red-100');
                    else if (btnStatus === 'absent') btn.classList.add('hover:bg-gray-300');
                }
            });
        }
    </script>
</x-app-layout>
