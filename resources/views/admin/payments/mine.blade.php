<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Payments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Payments</h3>
                    </div>

                    @if(isset($payments) && $payments->count() > 0)
                        <div class="divide-y">
                            @foreach($payments as $payment)
                                <div class="py-4 flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $payment->reference }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $payment->created_at->format('M d, Y') }} · {{ $payment->items->count() }} {{ Str::plural('student', $payment->items->count()) }}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <div class="text-right">
                                            <div class="font-semibold">GH₵{{ number_format($payment->total_amount, 2) }}</div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $payment->status_color }}-100 text-{{ $payment->status_color }}-800">
                                            {{ $payment->status_label }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $payments->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M9 21V3m6 18V3" />
                            </svg>
                            <h4 class="mt-2 text-sm font-medium text-gray-900">No payments yet</h4>
                            <p class="mt-1 text-sm text-gray-500">Your payment records will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
