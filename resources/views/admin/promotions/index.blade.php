<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Promote Students') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Card -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-gray-100">
                <div class="p-6 lg:p-8">
                    <!-- Session Messages -->
                    @if (session('success'))
                        <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 rounded-md shadow-sm">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md shadow-sm">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    <!-- Selection Form -->
                    <form action="{{ route('admin.promotions.index') }}" method="GET" class="mb-8">
                        <div class="flex flex-col sm:flex-row items-end gap-4 bg-gray-50 p-5 rounded-xl">
                            <div class="flex-1 w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Class</label>
                                <select name="from_class_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ (string) $fromClassId === (string) $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-150 shadow-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Load Class
                            </button>
                        </div>
                    </form>

                    @if($fromClassId)
                        <!-- Statistics Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 shadow-sm border border-blue-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-blue-700 uppercase tracking-wide">Total Students</div>
                                        <div class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['total'] ?? 0 }}</div>
                                    </div>
                                    <div class="p-3 bg-white rounded-full shadow-sm">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-xl p-5 shadow-sm border border-emerald-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-emerald-700 uppercase tracking-wide">Enrolled Students</div>
                                        <div class="text-3xl font-bold text-gray-800 mt-1">{{ $stats['enrolled'] ?? 0 }}</div>
                                    </div>
                                    <div class="p-3 bg-white rounded-full shadow-sm">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Promotion Form -->
                        <form action="{{ route('admin.promotions.store') }}" method="POST" onsubmit="return confirm('Promote all eligible students from this class to the selected destination?');" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Class</label>
                                    <select name="from_class_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150" required>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ (string) $fromClassId === (string) $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('from_class_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">To Class</label>
                                    <select name="to_class_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150" required>
                                        <option value="">Select destination class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('to_class_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <input id="only_enrolled" name="only_enrolled" type="checkbox" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" checked>
                                <label for="only_enrolled" class="ml-2 text-sm text-gray-700">Only promote students with status <span class="font-medium">"enrolled"</span></label>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-100">
                                <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150 shadow-md flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                    </svg>
                                    Promote Students
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-xl">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="mt-2 text-gray-500">Select a source class to view statistics and proceed with promotions.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>