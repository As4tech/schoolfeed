<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Successful') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <!-- Success Icon -->
                    <div class="mb-6">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Payment Completed!</h3>
                    <p class="text-gray-600 mb-6">Your feeding fee payment has been successfully processed.</p>

                    <!-- Payment Details -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-600">Reference</p>
                                <p class="font-semibold">{{ $payment->reference }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Date</p>
                                <p class="font-semibold">{{ $payment->paid_at?->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <h4 class="font-semibold text-gray-700 mb-3">Payment Breakdown</h4>
                            
                            @foreach($payment->items as $item)
                                <div class="flex justify-between py-2">
                                    <span class="text-gray-600">{{ $item->student->full_name }}</span>
                                    <span class="font-medium">GH₵{{ number_format($item->amount, 2) }}</span>
                                </div>
                            @endforeach

                            <div class="border-t mt-3 pt-3">
                                <div class="flex justify-between py-1">
                                    <span class="text-gray-600">Feeding Total</span>
                                    <span class="font-medium">GH₵{{ number_format($payment->school_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-1">
                                    <span class="text-orange-600">Platform Fee (1%)</span>
                                    <span class="font-medium text-orange-600">GH₵{{ number_format($payment->platform_fee, 2) }}</span>
                                </div>
                                <div class="flex justify-between py-2 text-lg font-bold">
                                    <span class="text-gray-900">Total Paid</span>
                                    <span class="text-green-600">GH₵{{ number_format($payment->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-center gap-4">
                        <a href="{{ route('dashboard') }}" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Go to Dashboard
                        </a>
                        <button onclick="window.print()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                            Print Receipt
                        </button>
                    </div>

                    <p class="mt-6 text-sm text-gray-500">
                        A confirmation email has been sent to {{ $payment->guardian->email ?? 'your email' }}.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
