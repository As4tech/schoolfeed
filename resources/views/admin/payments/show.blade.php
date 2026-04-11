<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium">Payment #{{ $payment->reference }}</h3>
                            <p class="text-sm text-gray-600">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Guardian</p>
                            <p class="font-medium">{{ $payment->guardian->name }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Payment Method</p>
                            <p class="font-medium capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $payment->status_color }}-100 text-{{ $payment->status_color }}-800">
                                {{ $payment->status_label }}
                            </span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Paid At</p>
                            <p class="font-medium">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-700 mb-3">Payment Breakdown</h4>
                        <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($payment->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->description ?? 'Feeding fee' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->student->full_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">GH₵{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Feeding Total:</td>
                                    <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">GH₵{{ number_format($payment->school_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="px-6 py-3 text-right text-sm font-medium text-orange-600">Platform Fee (1%):</td>
                                    <td class="px-6 py-3 text-right text-sm font-bold text-orange-600">GH₵{{ number_format($payment->platform_fee, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="px-6 py-3 text-right text-lg font-bold text-gray-900">Total Amount:</td>
                                    <td class="px-6 py-3 text-right text-lg font-bold text-green-600">GH₵{{ number_format($payment->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>

                    @if($payment->notes)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 mb-1">Notes</p>
                            <p class="text-gray-900">{{ $payment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
