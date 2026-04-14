<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Payment') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.payments.update', ['school' => request()->route('school'), 'payment' => $payment]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Guardian -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Guardian</label>
                                <select name="guardian_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Guardian</option>
                                    @foreach($guardians as $guardian)
                                        <option value="{{ $guardian->id }}" {{ old('guardian_id', $payment->guardian_id) == $guardian->id ? 'selected' : '' }}>
                                            {{ $guardian->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guardian_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                <select name="payment_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="mtn_momo" {{ old('payment_method', $payment->payment_method) == 'mtn_momo' ? 'selected' : '' }}>MTN Mobile Money</option>
                                    <option value="airtel_money" {{ old('payment_method', $payment->payment_method) == 'airtel_money' ? 'selected' : '' }}>Airtel Money</option>
                                    <option value="card" {{ old('payment_method', $payment->payment_method) == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                    <option value="bank" {{ old('payment_method', $payment->payment_method) == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ old('status', $payment->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount (GHS)</label>
                                <input type="number" name="amount" step="0.01" value="{{ old('amount', $payment->school_amount) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Paid At -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Paid At</label>
                                <input type="datetime-local" name="paid_at" 
                                    value="{{ old('paid_at', $payment->paid_at ? $payment->paid_at->format('Y-m-d\TH:i') : '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('paid_at')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $payment->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Items -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Items</h3>
                            <div id="payment-items" class="space-y-4">
                                @foreach($payment->items as $index => $item)
                                    <div class="payment-item flex gap-4 items-center">
                                        <select name="items[{{ $index }}][student_id]" required
                                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Select Student</option>
                                            @foreach($students as $student)
                                                <option value="{{ $student->id }}" {{ old("items.$index.student_id", $item->student_id) == $student->id ? 'selected' : '' }}>
                                                    {{ $student->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="number" name="items[{{ $index }}][amount]" step="0.01" 
                                            value="{{ old("items.$index.amount", $item->amount) }}" required
                                            placeholder="Amount" class="w-32 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <input type="text" name="items[{{ $index }}][description]" 
                                            value="{{ old("items.$index.description", $item->description) }}"
                                            placeholder="Description" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center gap-4 mt-6">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Update Payment
                            </button>
                            <a href="{{ route('admin.payments.show', ['school' => request()->route('school'), 'payment' => $payment]) }}" class="text-gray-600 hover:underline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
