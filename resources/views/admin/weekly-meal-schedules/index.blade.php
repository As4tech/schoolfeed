<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Weekly Meal Schedule Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <!-- Week Navigation -->
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <button type="button" id="previousWeek" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                ← Previous
                            </button>
                            <h3 class="text-lg font-semibold">
                                Week of {{ $weekStartDate->format('M d') }} - {{ $weekEndDate->format('M d, Y') }}
                            </h3>
                            <button type="button" id="nextWeek" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                Next →
                            </button>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <button type="button" id="copyWeekBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Copy from Previous Week
                            </button>
                            <button type="button" id="saveSchedule" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                Save Schedule
                            </button>
                        </div>
                    </div>

                    <!-- Meal Schedule Form -->
                    <form id="scheduleForm">
                        <input type="hidden" name="week_start_date" value="{{ $weekStartDate->format('Y-m-d') }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @php
                                $days = [
                                    'monday' => 'Monday',
                                    'tuesday' => 'Tuesday', 
                                    'wednesday' => 'Wednesday',
                                    'thursday' => 'Thursday',
                                    'friday' => 'Friday'
                                ];
                            @endphp

                            @foreach($days as $dayKey => $dayName)
                                <div class="border rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-900 mb-3">{{ $dayName }}</h4>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Meal</label>
                                            <select name="meals[{{ $dayKey }}][meal_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select a meal</option>
                                                @foreach($meals as $meal)
                                                    <option value="{{ $meal->id }}" 
                                                            data-price="{{ $meal->price ?? 10 }}"
                                                            {{ isset($schedules[$dayKey]) && $schedules[$dayKey]->first()->meal_id == $meal->id ? 'selected' : '' }}>
                                                        {{ $meal->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Price (GHS)</label>
                                            <input type="number" 
                                                   name="meals[{{ $dayKey }}][price]" 
                                                   step="0.01" 
                                                   min="0"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                   value="{{ isset($schedules[$dayKey]) ? $schedules[$dayKey]->first()->price : '10.00' }}"
                                                   placeholder="10.00">
                                        </div>

                                        <input type="hidden" name="meals[{{ $dayKey }}][day_of_week]" value="{{ $dayKey }}">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Week Preview -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Week Preview</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        @foreach($days as $dayKey => $dayName)
                            <div class="text-center p-4 bg-gray-50 rounded-lg">
                                <h5 class="font-medium text-gray-900 mb-2">{{ $dayName }}</h5>
                                @if(isset($schedules[$dayKey]) && $schedules[$dayKey]->isNotEmpty())
                                    <p class="text-sm text-gray-700">{{ $schedules[$dayKey]->first()->meal->name }}</p>
                                    <p class="text-sm font-semibold text-green-600 mt-1">GHS {{ number_format($schedules[$dayKey]->first()->price, 2) }}</p>
                                @else
                                    <p class="text-sm text-gray-500">No meal scheduled</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Copy Week Modal -->
    <div id="copyWeekModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-md mx-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Copy Week Schedule</h3>
            <p class="text-sm text-gray-600 mb-4">Select a week to copy the meal schedule from:</p>
            
            <form id="copyWeekForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Week</label>
                    <input type="date" 
                           name="from_week_start_date" 
                           id="fromWeekDate"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>
                
                <input type="hidden" name="to_week_start_date" value="{{ $weekStartDate->format('Y-m-d') }}">
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCopyModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Copy Schedule
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const weekStartDate = new Date('{{ $weekStartDate->format("Y-m-d") }}');
            const currentWeekDisplay = document.querySelector('h3');
            const previousWeekBtn = document.getElementById('previousWeek');
            const nextWeekBtn = document.getElementById('nextWeek');
            const saveScheduleBtn = document.getElementById('saveSchedule');
            const copyWeekBtn = document.getElementById('copyWeekBtn');
            const copyWeekModal = document.getElementById('copyWeekModal');
            const copyWeekForm = document.getElementById('copyWeekForm');
            const scheduleForm = document.getElementById('scheduleForm');

            // Update price when meal is selected (prefill from meal default price, fallback 10.00)
            document.querySelectorAll('select[name^="meals"][name$="[meal_id]"]').forEach(select => {
                select.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const priceInput = this.closest('.space-y-3').querySelector('input[type="number"]');
                    if (selectedOption && selectedOption.value) {
                        const mealDefault = selectedOption.dataset.price;
                        priceInput.value = mealDefault && mealDefault !== '' ? mealDefault : '10.00';
                    }
                });
            });

            // Navigate weeks
            function updateWeekDisplay(date) {
                const endDate = new Date(date);
                endDate.setDate(endDate.getDate() + 4);
                
                currentWeekDisplay.textContent = `Week of ${date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
                
                document.querySelector('input[name="week_start_date"]').value = date.toISOString().split('T')[0];
                
                // Load week data
                loadWeekData(date);
            }

            function loadWeekData(date) {
                const weekStart = date.toISOString().split('T')[0];
                
                fetch(`/admin/weekly-meal-schedules/show-week?week_start_date=${weekStart}`)
                    .then(response => response.json())
                    .then(data => {
                        // Update form with loaded data
                        Object.keys(data.schedules).forEach(dayKey => {
                            const schedule = data.schedules[dayKey][0]; // Get first schedule for the day
                            if (schedule) {
                                const mealSelect = document.querySelector(`select[name="meals[${dayKey}][meal_id]"]`);
                                const priceInput = document.querySelector(`input[name="meals[${dayKey}][price]"]`);
                                
                                if (mealSelect) mealSelect.value = schedule.meal_id;
                                if (priceInput) priceInput.value = schedule.price;
                            }
                        });
                    })
                    .catch(error => console.error('Error loading week data:', error));
            }

            previousWeekBtn.addEventListener('click', function() {
                const newDate = new Date(weekStartDate);
                newDate.setDate(newDate.getDate() - 7);
                weekStartDate.setTime(newDate.getTime());
                updateWeekDisplay(newDate);
            });

            nextWeekBtn.addEventListener('click', function() {
                const newDate = new Date(weekStartDate);
                newDate.setDate(newDate.getDate() + 7);
                weekStartDate.setTime(newDate.getTime());
                updateWeekDisplay(newDate);
            });

            // Save schedule
            saveScheduleBtn.addEventListener('click', function() {
                const formData = new FormData(scheduleForm);
                
                // Build the data structure manually to handle nested fields
                const data = {
                    week_start_date: formData.get('week_start_date'),
                    meals: {}
                };
                
                // Extract meals data
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
                days.forEach(day => {
                    const mealId = formData.get(`meals[${day}][meal_id]`);
                    const price = formData.get(`meals[${day}][price]`);
                    
                    if (mealId && mealId !== '') {
                        data.meals[day] = {
                            meal_id: parseInt(mealId),
                            price: parseFloat(price)
                        };
                    }
                });
                
                fetch('{{ route('admin.weekly-meal-schedules.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error saving schedule');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error saving schedule');
                });
            });

            // Copy week functionality
            copyWeekBtn.addEventListener('click', function() {
                copyWeekModal.classList.remove('hidden');
                
                // Set default to previous week
                const previousWeek = new Date(weekStartDate);
                previousWeek.setDate(previousWeek.getDate() - 7);
                document.getElementById('fromWeekDate').value = previousWeek.toISOString().split('T')[0];
            });

            copyWeekForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('{{ route('admin.weekly-meal-schedules.copy-week') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData)),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error copying week schedule');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error copying week schedule');
                });
            });
        });

        function closeCopyModal() {
            document.getElementById('copyWeekModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
