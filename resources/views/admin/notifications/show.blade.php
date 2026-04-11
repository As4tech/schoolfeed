<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notification Details') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            @if(str_contains($notification->type, 'PaymentSuccess'))
                                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            @elseif(str_contains($notification->type, 'PaymentReminder'))
                                <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            @else
                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-6 flex-1">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ $notification->data['message'] ?? 'Notification' }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Received {{ $notification->created_at->diffForHumans() }}
                            </p>
                            
                            <div class="mt-6 space-y-4">
                                @if(isset($notification->data['reference']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Reference:</span>
                                        <span class="text-sm text-gray-900 ml-2">{{ $notification->data['reference'] }}</span>
                                    </div>
                                @endif
                                @if(isset($notification->data['amount']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Amount:</span>
                                        <span class="text-sm text-gray-900 ml-2">GH₵{{ number_format($notification->data['amount'], 2) }}</span>
                                    </div>
                                @endif
                                @if(isset($notification->data['status']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Status:</span>
                                        <span class="text-sm text-gray-900 ml-2">{{ ucfirst($notification->data['status']) }}</span>
                                    </div>
                                @endif
                                @if(isset($notification->data['school_name']))
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">School:</span>
                                        <span class="text-sm text-gray-900 ml-2">{{ $notification->data['school_name'] }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6 flex gap-4">
                                @if(isset($notification->data['action_url']))
                                    <a href="{{ $notification->data['action_url'] }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-all">
                                        {{ $notification->data['action_text'] ?? 'View Details' }}
                                    </a>
                                @endif
                                <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all">
                                    Back to Notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
