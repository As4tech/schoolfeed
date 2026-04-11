<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Notifications') }}</h2>
                <p class="text-sm text-gray-600 mt-1">View and manage your notifications</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all {{ request()->routeIs('admin.notifications.index') ? 'bg-blue-100 text-blue-700' : '' }}">All</a>
                <a href="{{ route('admin.notifications.unread') }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-all {{ request()->routeIs('admin.notifications.unread') ? 'bg-blue-100 text-blue-700' : '' }}">
                    Unread
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="ml-1 px-2 py-0.5 bg-red-500 text-white text-xs rounded-full">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </a>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600 transition-all">
                            Mark All Read
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    @if($notifications->count() > 0)
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="flex items-start p-4 rounded-lg {{ $notification->read() ? 'bg-gray-50' : 'bg-blue-50 border border-blue-200' }}">
                                    <div class="flex-shrink-0">
                                        @if(str_contains($notification->type, 'PaymentSuccess'))
                                            <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        @elseif(str_contains($notification->type, 'PaymentReminder'))
                                            <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $notification->data['message'] ?? 'New notification' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                        @if($notification->unread())
                                            <div class="mt-2 flex gap-2">
                                                @if(isset($notification->data['action_url']))
                                                    <a href="{{ route('admin.notifications.show', $notification) }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                        {{ $notification->data['action_text'] ?? 'View Details' }} &rarr;
                                                    </a>
                                                @endif
                                                <form action="{{ route('admin.notifications.mark-read', $notification) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                                        Mark as read
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-600">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                            <p class="mt-1 text-sm text-gray-500">You don't have any notifications at the moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
