<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Parent Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">My Children</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $children->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Active Plans</p>
                                @php
                                    $activePlans = $children->sum(fn($c) => $c->feedingPlans->count());
                                @endphp
                                <p class="text-2xl font-bold text-green-600">{{ $activePlans }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Spent</p>
                                <p class="text-2xl font-bold text-purple-600">GH₵{{ number_format($totalSpent, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meal Selection & Payment Summary -->
            @if($children->isNotEmpty())
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Left: Meal Selection -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Meal Selection</h3>

                    <!-- Child Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Child</label>
                        <select id="childSelect" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach($children as $child)
                                <option value="{{ $child->id }}" {{ $children->first()->id == $child->id ? 'selected' : '' }}>
                                    {{ $child->full_name }} (Grade {{ $child->grade }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Week Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-medium text-gray-800">
                            Week of {{ $weekStartDate->format('M d') }} - {{ $weekStartDate->copy()->addDays(4)->format('M d') }}
                        </h4>
                        <button type="button" id="deselectAll" class="text-sm text-red-600 hover:text-red-800">Deselect All</button>
                    </div>

                    <!-- Meals List -->
                    <div id="mealSchedule" class="space-y-3">
                        @php($days = ['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday'])
                        @foreach($days as $dayKey => $dayName)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <input type="checkbox"
                                               id="meal_{{ $dayKey }}"
                                               class="meal-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               data-day="{{ $dayKey }}"
                                               value="{{ $weeklySchedules[$dayKey]->first()->id ?? '' }}"
                                               data-price="{{ $weeklySchedules[$dayKey]->first()->price ?? 0 }}"
                                               {{ isset($existingSelections[$children->first()->id . '_' . $dayKey]) ? 'checked' : '' }}>
                                        <label for="meal_{{ $dayKey }}" class="ml-3 flex-1 cursor-pointer">
                                            <div class="flex justify-between items-center">
                                                <div>
                                                    <span class="font-medium text-gray-900">{{ $dayName }}:</span>
                                                    @if(isset($weeklySchedules[$dayKey]) && $weeklySchedules[$dayKey]->isNotEmpty())
                                                        <span class="text-gray-700 ml-2">{{ $weeklySchedules[$dayKey]->first()->meal->name }}</span>
                                                    @else
                                                        <span class="text-gray-500 ml-2">No meal available</span>
                                                    @endif
                                                </div>
                                                @if(isset($weeklySchedules[$dayKey]) && $weeklySchedules[$dayKey]->isNotEmpty())
                                                    <span class="text-sm font-semibold text-green-600">GHS {{ number_format($weeklySchedules[$dayKey]->first()->price, 2) }}</span>
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Right: Payment Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Payment Summary</h3>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Meals selected:</span>
                            <span id="mealsCount" class="font-medium">0 days</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span id="subtotal" class="font-medium">GHS 0.00</span>
                        </div>
                                                <div class="border-t pt-4">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-900">Total:</span>
                                <span id="totalAmount" class="text-lg font-bold text-green-600">GHS 0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select id="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="mtn_momo">MTN Mobile Money</option>
                            <option value="airtel_money">Airtel Money</option>
                            <option value="vodafone_cash">Vodafone Cash</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                        <input type="tel" id="phoneNumber" placeholder="024 123 4567" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" maxlength="15">
                    </div>

                    <button type="button" id="payButton" class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition-colors font-medium disabled:bg-gray-400 disabled:cursor-not-allowed" disabled>
                        Pay GHS 0.00
                    </button>

                    <div class="mt-4 text-center">
                        <a href="{{ route('parent.meals.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">Go to Full Meal Selection</a>
                    </div>
                </div>
            </div>

            <!-- Loading Overlay & Success Modal -->
            <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-6 flex items-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
                    <span>Processing...</span>
                </div>
            </div>

            <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-8 max-w-md mx-4">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Payment Successful!</h3>
                        <p class="text-sm text-gray-500 mb-6">Your meal selection has been confirmed and payment processed successfully.</p>
                        <button type="button" onclick="window.location.reload()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Close</button>
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

                    function updatePaymentSummary() {
                        const checkedBoxes = document.querySelectorAll('.meal-checkbox:checked');
                        const mealsCount = checkedBoxes.length;
                        let subtotal = 0;
                        checkedBoxes.forEach(checkbox => { subtotal += parseFloat(checkbox.dataset.price || 0); });
                        // Total is now just the subtotal (platform fee will be added at checkout)
                        const total = subtotal;
                        document.getElementById('mealsCount').textContent = mealsCount + ' days';
                        document.getElementById('subtotal').textContent = 'GHS ' + subtotal.toFixed(2);
                        document.getElementById('totalAmount').textContent = 'GHS ' + total.toFixed(2);
                        payButton.textContent = 'Pay GHS ' + total.toFixed(2);
                        payButton.disabled = mealsCount === 0;
                    }

                    if (deselectAllBtn) {
                        deselectAllBtn.addEventListener('click', function() {
                            document.querySelectorAll('.meal-checkbox').forEach(cb => cb.checked = false);
                            updatePaymentSummary();
                        });
                    }

                    mealCheckboxes.forEach(cb => cb.addEventListener('change', updatePaymentSummary));

                    if (childSelect) {
                        childSelect.addEventListener('change', function() {
                            const studentId = this.value;
                            loadWeekMeals(studentId);
                        });
                    }

                    function loadWeekMeals(studentId) {
                        const weekStartDate = '{{ $weekStartDate->format('Y-m-d') }}';
                        fetch(`/parent/meals/week-meals?week_start_date=${weekStartDate}&student_id=${studentId}`)
                            .then(r => r.json())
                            .then(data => {
                                if (data.error) return;
                                Object.keys(data.weekly_schedules).forEach(dayKey => {
                                    const checkbox = document.getElementById(`meal_${dayKey}`);
                                    const selectionKey = studentId + '_' + dayKey;
                                    if (checkbox && data.weekly_schedules[dayKey].length > 0) {
                                        const schedule = data.weekly_schedules[dayKey][0];
                                        checkbox.value = schedule.id;
                                        checkbox.dataset.price = schedule.price;
                                        checkbox.checked = Object.prototype.hasOwnProperty.call(data.existing_selections, selectionKey);
                                        checkbox.disabled = false;

                                        const label = checkbox.nextElementSibling;
                                        const mealNameSpan = label.querySelector('.text-gray-700');
                                        const priceSpan = label.querySelector('.text-green-600');
                                        if (mealNameSpan) mealNameSpan.textContent = ' ' + schedule.meal.name;
                                        if (priceSpan) priceSpan.textContent = 'GHS ' + parseFloat(schedule.price).toFixed(2);
                                    } else if (checkbox) {
                                        checkbox.disabled = true;
                                        checkbox.checked = false;
                                    }
                                });
                                updatePaymentSummary();
                            });
                    }

                    if (payButton) {
                        payButton.addEventListener('click', function() {
                            const studentId = childSelect.value;
                            const paymentMethod = document.getElementById('paymentMethod').value;
                            const phoneNumber = document.getElementById('phoneNumber').value;
                            if (!phoneNumber.trim()) { alert('Please enter your mobile number'); return; }
                            const selectedMeals = Array.from(document.querySelectorAll('.meal-checkbox:checked')).map(cb => cb.value);
                            if (selectedMeals.length === 0) { alert('Please select at least one meal'); return; }

                            loadingOverlay.classList.remove('hidden');

                            fetch('/parent/meals', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                                body: JSON.stringify({ student_id: studentId, selections: selectedMeals })
                            })
                            .then(r => r.json())
                            .then(d => {
                                if (!d.success) throw new Error(d.error || 'Failed to save meal selections');
                                return fetch('/parent/meals/payment', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                                    body: JSON.stringify({ student_id: studentId, payment_method: paymentMethod, phone_number: phoneNumber })
                                });
                            })
                            .then(r => r.json())
                            .then(d => {
                                loadingOverlay.classList.add('hidden');
                                if (d.success && d.authorization_url) {
                                    // Redirect to Paystack checkout
                                    window.location = d.authorization_url;
                                    return;
                                }
                                if (d.success) { 
                                    // Fallback success modal (should not normally occur when using Paystack)
                                    successModal.classList.remove('hidden'); 
                                } else { 
                                    alert(d.error || 'Payment failed.'); 
                                }
                            })
                            .catch(err => { loadingOverlay.classList.add('hidden'); alert('An error occurred. Please try again.'); console.error(err); });
                        });
                    }

                    updatePaymentSummary();
                });
            </script>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- My Children -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">My Children</h3>
                            <a href="{{ request()->route('school') ? route('admin.students.index', ['school' => request()->route('school')]) : '#' }}" class="text-blue-600 hover:underline text-sm">View All</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($children as $child)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                            {{ strtoupper(substr($child->first_name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $child->full_name }}</p>
                                            <p class="text-xs text-gray-500">{{ $child->school->name }} • Grade {{ $child->grade }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($child->feedingPlans->count() > 0)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Active Plan
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                No Plan
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No children registered yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                            <a href="{{ route('admin.payments.mine') }}" class="text-blue-600 hover:underline text-sm">View All</a>
                        </div>
                        <div class="space-y-4">
                            @forelse($payments as $payment)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $payment->reference }}</p>
                                        <p class="text-xs text-gray-500">{{ $payment->created_at->format('M d, Y') }} • {{ $payment->items->count() }} students</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900">GH₵{{ number_format($payment->total_amount, 2) }}</p>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $payment->status_color }}-100 text-{{ $payment->status_color }}-800">
                                            {{ $payment->status_label }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No payments recorded yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Renewals -->
            @if($upcomingPayments->count() > 0)
                <div class="mt-8 bg-yellow-50 overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-4">Plans Expiring Soon</h3>
                        <div class="space-y-3">
                            @foreach($upcomingPayments as $student)
                                @foreach($student->feedingPlans as $plan)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">{{ $student->full_name }} - {{ $plan->name }}</span>
                                        </div>
                                        <span class="text-sm text-yellow-600 font-semibold">Expires {{ \Carbon\Carbon::parse($plan->pivot->end_date)->format('M d') }}</span>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
