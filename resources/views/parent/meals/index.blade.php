<x-app-layout>
    @php
        // Add cache-busting timestamp
        $cacheBuster = time();
    @endphp
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Meal Selection & Payment
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Section: Meal Selection -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Meal Selection</h3>
                    
                    <!-- Child Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Child</label>
                        <select id="childSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($children as $child)
                                <option value="{{ $child->id }}" {{ $children->first()->id == $child->id ? 'selected' : '' }}>
                                    {{ $child->full_name }} ({{ $child->grade }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Meal Schedule -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-medium text-gray-800">
                                Week of {{ $weekStartDate->format('M d') }} - {{ $weekStartDate->copy()->addDays(4)->format('M d') }}
                            </h4>
                            <button type="button" id="deselectAll" class="text-sm text-red-600 hover:text-red-800">
                                Deselect All
                            </button>
                        </div>

                        <div id="mealSchedule" class="space-y-3">
                            @php
                                $days = ['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday'];
                            @endphp

                            @foreach($days as $dayKey => $dayName)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <input type="checkbox" 
                                                   id="meal_{{ $dayKey }}" 
                                                   name="meals[]" 
                                                   value="{{ $weeklySchedules[$dayKey]->first()->id ?? '' }}"
                                                   class="meal-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                   data-day="{{ $dayKey }}"
                                                   data-price="{{ $weeklySchedules[$dayKey]->first()->price ?? 0 }}"
                                                   {{ isset($existingSelections[$children->first()->id . '_' . $dayKey]) ? 'checked' : '' }}>
                                            
                                            <label for="meal_{{ $dayKey }}" class="ml-3 flex-1 cursor-pointer">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <span class="font-medium text-gray-900">{{ $dayName }}:</span>
                                                        @if(isset($weeklySchedules[$dayKey]) && $weeklySchedules[$dayKey]->isNotEmpty())
                                                            <span class="text-gray-700 ml-2">
                                                                {{ $weeklySchedules[$dayKey]->first()->meal->name }}
                                                            </span>
                                                        @else
                                                            <span class="text-gray-500 ml-2">No meal available</span>
                                                        @endif
                                                    </div>
                                                    @if(isset($weeklySchedules[$dayKey]) && $weeklySchedules[$dayKey]->isNotEmpty())
                                                        <span class="text-sm font-semibold text-green-600">
                                                            GHS {{ number_format($weeklySchedules[$dayKey]->first()->price, 2) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Section: Payment Summary -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Payment Summary</h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Meals selected:</span>
                            <span id="mealsCount" class="font-medium">0 days</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal (GHS <span id="pricePerMeal">10</span>/day):</span>
                            <span id="subtotal" class="font-medium">GHS 0.00</span>
                        </div>
                        
                                                
                        <div class="border-t pt-4">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-900">Total:</span>
                                <span id="totalAmount" class="text-lg font-bold text-green-600">GHS 0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select id="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="mtn_momo">MTN Mobile Money</option>
                            <option value="airtel_money">Airtel Money</option>
                            <option value="vodafone_cash">Vodafone Cash</option>
                        </select>
                    </div>

                    <!-- Mobile Number -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                        <input type="tel" 
                               id="phoneNumber" 
                               placeholder="024 123 4567" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               maxlength="15">
                    </div>

                    <!-- Pay Button -->
                    <button type="button" 
                            id="payButton" 
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition-colors font-medium disabled:bg-gray-400 disabled:cursor-not-allowed"
                            disabled>
                        Pay GHS 0.00
                    </button>

                    <!-- Alternative Payment Option -->
                    <div class="mt-4 text-center">
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                            Pay for Full Term Instead?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
            <span>Processing...</span>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-md mx-4">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Payment Successful!</h3>
                <p class="text-sm text-gray-500 mb-6">Your meal selection has been confirmed and payment processed successfully.</p>
                <button type="button" onclick="window.location.reload()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const childSelect = document.getElementById('childSelect');
            const mealCheckboxes = document.querySelectorAll('.meal-checkbox');
            const deselectAllBtn = document.getElementById('deselectAll');
            const payButton = document.getElementById('payButton');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const successModal = document.getElementById('successModal');

            // Update payment summary
            function updatePaymentSummary() {
                const checkedBoxes = document.querySelectorAll('.meal-checkbox:checked');
                const mealsCount = checkedBoxes.length;
                let subtotal = 0;

                checkedBoxes.forEach(checkbox => {
                    subtotal += parseFloat(checkbox.dataset.price || 0);
                });

                // Total is now just the subtotal (platform fee will be added at checkout)
                const total = subtotal;

                document.getElementById('mealsCount').textContent = mealsCount + ' days';
                document.getElementById('subtotal').textContent = 'GHS ' + subtotal.toFixed(2);
                document.getElementById('totalAmount').textContent = 'GHS ' + total.toFixed(2);
                payButton.textContent = 'Pay GHS ' + total.toFixed(2);
                payButton.disabled = mealsCount === 0;
            }

            // Deselect all meals
            deselectAllBtn.addEventListener('click', function() {
                mealCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updatePaymentSummary();
            });

            // Update summary on checkbox change
            mealCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updatePaymentSummary);
            });

            // Handle child selection change
            childSelect.addEventListener('change', function() {
                const studentId = this.value;
                loadWeekMeals(studentId);
            });

            // Load week meals for selected student
            function loadWeekMeals(studentId) {
                const weekStartDate = '{{ $weekStartDate->format("Y-m-d") }}';
                
                fetch(`/parent/meals/week-meals?week_start_date=${weekStartDate}&student_id=${studentId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        // Update meal checkboxes
                        Object.keys(data.weekly_schedules).forEach(dayKey => {
                            const checkbox = document.getElementById(`meal_${dayKey}`);
                            const selectionKey = studentId + '_' + dayKey;
                            
                            if (checkbox && data.weekly_schedules[dayKey].length > 0) {
                                const schedule = data.weekly_schedules[dayKey][0];
                                checkbox.value = schedule.id;
                                checkbox.dataset.price = schedule.price;
                                checkbox.checked = data.existing_selections.hasOwnProperty(selectionKey);
                                checkbox.disabled = false;
                                
                                // Update meal name and price display
                                const label = checkbox.nextElementSibling;
                                const mealNameSpan = label.querySelector('.text-gray-700');
                                const priceSpan = label.querySelector('.text-green-600');
                                
                                if (mealNameSpan) {
                                    mealNameSpan.textContent = ' ' + schedule.meal.name;
                                }
                                if (priceSpan) {
                                    priceSpan.textContent = 'GHS ' + parseFloat(schedule.price).toFixed(2);
                                }
                            } else if (checkbox) {
                                checkbox.disabled = true;
                                checkbox.checked = false;
                            }
                        });

                        updatePaymentSummary();
                    })
                    .catch(error => console.error('Error loading week meals:', error));
            }

            // Handle payment
            payButton.addEventListener('click', function() {
                const studentId = childSelect.value;
                const paymentMethod = document.getElementById('paymentMethod').value;
                const phoneNumber = document.getElementById('phoneNumber').value;

                if (!phoneNumber.trim()) {
                    alert('Please enter your mobile number');
                    return;
                }

                // Get selected meal IDs
                const selectedMeals = Array.from(document.querySelectorAll('.meal-checkbox:checked'))
                    .map(checkbox => checkbox.value);

                if (selectedMeals.length === 0) {
                    alert('Please select at least one meal');
                    return;
                }

                loadingOverlay.classList.remove('hidden');

                // First, save meal selections
                fetch('/parent/meals', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        student_id: studentId,
                        selections: selectedMeals
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Process payment
                        return fetch('/parent/meals/payment', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                student_id: studentId,
                                payment_method: paymentMethod,
                                phone_number: phoneNumber
                            })
                        });
                    } else {
                        throw new Error(data.error || 'Failed to save meal selections');
                    }
                })
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.classList.add('hidden');
                    if (data.success && data.authorization_url) {
                        // Redirect to Paystack checkout
                        window.location = data.authorization_url;
                        return;
                    }
                    if (data.success) {
                        // Fallback success modal (should not normally occur when using Paystack)
                        successModal.classList.remove('hidden');
                    } else {
                        alert(data.error || 'Payment failed. Please try again.');
                    }
                })
                .catch(error => {
                    loadingOverlay.classList.add('hidden');
                    console.error('Payment error:', error);
                    alert('An error occurred. Please try again.');
                });
            });

            // Initialize payment summary
            updatePaymentSummary();
        });
    </script>
</x-app-layout>
