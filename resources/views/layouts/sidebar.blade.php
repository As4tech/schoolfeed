<aside class="fixed left-0 top-0 w-72 h-full bg-gray-800 text-white shadow-lg z-30 transform -translate-x-full transition-transform duration-200 md:translate-x-0"
       x-bind:class="{ 'translate-x-0': sidebarOpen }">
    <!-- Logo Section -->
    <div class="h-16 flex items-center justify-center bg-gray-900 border-b border-gray-700">
        @php 
            // Check if we're in a Super Admin global context
            $isSuperAdminGlobal = auth()->user()->hasRole('Super Admin') && request()->routeIs('superadmin.*');
            
            $tenantSchool = request()->route('school');
            $tenantSlug = is_object($tenantSchool) ? ($tenantSchool->slug ?? null) : (is_string($tenantSchool) ? $tenantSchool : null);
            
            // For Super Admin global routes, force tenantSlug to null
            if ($isSuperAdminGlobal) {
                $tenantSlug = null;
            }
            
            // Centralized school slug helper for all roles
            function getSchoolSlugForSidebar() {
                if (request()->route('school')) {
                    $school = request()->route('school');
                    return is_object($school) ? $school->slug : $school;
                }
                
                $user = auth()->user();
                if ($user) {
                    // For parents, get from guardian
                    if ($user->hasRole('Parent') && $user->guardian_id) {
                        $guardian = \App\Models\Guardian::find($user->guardian_id);
                        if ($guardian && $guardian->school) {
                            return $guardian->school->slug;
                        }
                    }
                    // For other roles, get from school_id
                    elseif ($user->school_id) {
                        $school = \App\Models\School::find($user->school_id);
                        if ($school) {
                            return $school->slug;
                        }
                    }
                }
                
                return null;
            }
            
            $schoolSlugForSidebar = getSchoolSlugForSidebar();
        @endphp
        <a href="{{ $tenantSlug ? route('dashboard', ['school' => $tenantSlug]) : route('superadmin.dashboard') }}" class="text-xl font-bold text-white">
            <span class="text-indigo-400">School</span>Feed
        </a>
    </div>

    <!-- Navigation Links -->
    <nav class="mt-4 px-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">
        <!-- Dashboard -->
        <a href="{{ $tenantSlug ? route('dashboard', ['school' => $tenantSlug]) : route('superadmin.dashboard') }}" 
           class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ ($tenantSlug && request()->routeIs('dashboard')) || (!$tenantSlug && request()->routeIs('superadmin.dashboard')) ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            Dashboard
        </a>

        @role('Super Admin')
            <!-- Super Admin Quick Links (no separate section header) -->
            <div class="pt-4 mt-4 border-t border-gray-700">
                <a href="{{ route('superadmin.schools.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('superadmin.schools.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Schools
                </a>

                <!-- Inventory & Kitchen -->
                @php
                    $user = auth()->user();
                    $invLowCount = 0;
                    try {
                        if (\Schema::hasTable('inventory_items')) {
                            $invLowCount = \App\Models\InventoryItem::when(!$user->hasRole('Super Admin'), function($q) use ($user) {
                                return $q->where('school_id', $user->school_id);
                            })->whereColumn('quantity', '<=', 'min_level')->count();
                        }
                    } catch (\Throwable $e) {
                        $invLowCount = 0;
                    }
                @endphp
                @if($tenantSlug)
                <a href="{{ route('admin.inventory.dashboard') }}" 
                   class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.inventory.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2h-3l-2-2H9L7 5H4a2 2 0 00-2 2v6m18 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4m18 0H2"/>
                        </svg>
                        Inventory Dashboard
                    </span>
                    @if($invLowCount > 0)
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full bg-red-600 text-white">{{ $invLowCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.inventory.items.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.inventory.items.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                    </svg>
                    Inventory Items
                </a>
                @endif

                @if($tenantSlug)
                <a href="{{ route('admin.promotions.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.promotions.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8M11 17H3m0 0V9m0 8l8-8" />
                    </svg>
                    Promote
                </a>
                @endif

                <a href="{{ route('superadmin.insights.schools') }}"
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('superadmin.insights.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18m8-8H3"/>
                    </svg>
                    Insights
                </a>

                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('profile.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Settings
                </a>
            </div>
        @endrole

        @role(['Accountant'])
            <!-- Inventory Access for Accountant -->
            <div class="pt-4 mt-4 border-t border-gray-700">
                @php
                    $user = auth()->user();
                    $invLowCountAcc = 0;
                    try {
                        if (\Schema::hasTable('inventory_items')) {
                            $invLowCountAcc = \App\Models\InventoryItem::when(!$user->hasRole('Super Admin'), function($q) use ($user) {
                                return $q->where('school_id', $user->school_id);
                            })->whereColumn('quantity', '<=', 'min_level')->count();
                        }
                    } catch (\Throwable $e) {
                        $invLowCountAcc = 0;
                    }
                @endphp
                <a href="{{ route('admin.inventory.dashboard') }}" 
                   class="flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.inventory.dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2h-3l-2-2H9L7 5H4a2 2 0 00-2 2v6m18 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4m18 0H2"/>
                        </svg>
                        Inventory Dashboard
                    </span>
                    @if($invLowCountAcc > 0)
                        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full bg-red-600 text-white">{{ $invLowCountAcc }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.inventory.items.index') }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.inventory.items.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                    </svg>
                    Inventory Items
                </a>
                            </div>
        @endrole

        @role(['School Admin'])
            <!-- Academics Dropdown -->
            <div x-data="{ isOpen: {{ request()->routeIs('admin.students.*') || request()->routeIs('admin.classes.*') || request()->routeIs('admin.promotions.*') ? 'true' : 'false' }} }" class="pt-4 mt-4 border-t border-gray-700">
                <button @click="isOpen = !isOpen" 
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-300 hover:bg-gray-700 hover:text-white">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Academics
                    </span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="isOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="mt-1 space-y-1">
                    <a href="{{ $schoolSlugForSidebar ? route('admin.students.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.students.*') ? 'bg-gray-700 text-white' : '' }}">
                        Students
                    </a>
                    <a href="{{ $schoolSlugForSidebar ? route('admin.classes.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.classes.*') ? 'bg-gray-700 text-white' : '' }}">
                        Classes
                    </a>
                    <a href="{{ $schoolSlugForSidebar ? route('admin.promotions.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.promotions.*') ? 'bg-gray-700 text-white' : '' }}">
                        Promote
                    </a>
                </div>
            </div>
            
            <!-- Canteen Dropdown -->
            <div x-data="{ isOpen: {{ request()->routeIs('admin.inventory.*') || request()->routeIs('admin.feeding-plans.*') || request()->routeIs('admin.daily-roster.*') || request()->routeIs('admin.meals.*') || request()->routeIs('admin.weekly-meal-schedules.*') || request()->routeIs('admin.feeding-attendance.*') ? 'true' : 'false' }} }" class="pt-2">
                @php
                    $user = auth()->user();
                    $invLowCountSchool = 0;
                    try {
                        if (\Schema::hasTable('inventory_items')) {
                            $invLowCountSchool = \App\Models\InventoryItem::when(!$user->hasRole('Super Admin'), function($q) use ($user) {
                                return $q->where('school_id', $user->school_id);
                            })->whereColumn('quantity', '<=', 'min_level')->count();
                        }
                    } catch (\Throwable $e) {
                        $invLowCountSchool = 0;
                    }
                @endphp
                <button @click="isOpen = !isOpen" 
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-300 hover:bg-gray-700 hover:text-white">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Canteen
                    </span>
                    @if($invLowCountSchool > 0)
                        <span class="mr-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-semibold rounded-full bg-red-600 text-white">{{ $invLowCountSchool }}</span>
                    @endif
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="isOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="mt-1 space-y-1">
                    <a href="{{ $schoolSlugForSidebar ? route('admin.inventory.dashboard', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.inventory.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                        Inventory Dashboard
                    </a>
                    <a href="{{ $schoolSlugForSidebar ? route('admin.inventory.items.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.inventory.items.*') ? 'bg-gray-700 text-white' : '' }}">
                        Inventory Items
                    </a>
                    <a href="{{ $schoolSlugForSidebar ? route('admin.daily-roster.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.daily-roster.*') ? 'bg-gray-700 text-white' : '' }}">
                        Daily Roster
                    </a>
                    <a href="{{ $schoolSlugForSidebar ? route('admin.feeding-plans.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.feeding-plans.*') ? 'bg-gray-700 text-white' : '' }}">
                        Feeding Plans
                    </a>
                    <a href="{{ $schoolSlugForSidebar ? route('admin.meals.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.meals.*') ? 'bg-gray-700 text-white' : '' }}">
                        Meals
                    </a>
                    <a href="{{ $schoolSlugForSidebar ? route('admin.weekly-meal-schedules.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.weekly-meal-schedules.*') ? 'bg-gray-700 text-white' : '' }}">
                        Meals Schedule
                    </a>
                    <a href="{{ $schoolSlugForSidebar ? route('admin.feeding-attendance.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.feeding-attendance.*') ? 'bg-gray-700 text-white' : '' }}">
                        Daily Attendance
                    </a>
                </div>
            </div>
        @endrole

        @role(['Accountant', 'School Admin'])
            <!-- Accounting Dropdown -->
            <div x-data="{ isOpen: {{ request()->routeIs('admin.accountant.dashboard') || request()->routeIs('admin.payments.*') ? 'true' : 'false' }} }" class="pt-2">
                <button @click="isOpen = !isOpen" 
                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors text-gray-300 hover:bg-gray-700 hover:text-white">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Accounting
                    </span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="isOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                     class="mt-1 space-y-1">
                    <a href="{{ $schoolSlugForSidebar ? route('admin.accountant.dashboard', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.accountant.dashboard') ? 'bg-gray-700 text-white' : '' }}">
                        Accounting Dashboard
                    </a>
                    <a href="{{ $schoolSlugForSidebar ? route('admin.payments.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                       class="flex items-center pl-12 pr-4 py-2 text-sm text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white {{ request()->routeIs('admin.payments.*') ? 'bg-gray-700 text-white' : '' }}">
                        Payments
                    </a>
                </div>
            </div>
        @endrole
        
        @role(['School Admin', 'Accountant'])
            <!-- Reports -->
            <div class="pt-2">
                <a href="{{ $schoolSlugForSidebar ? route('admin.reports.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reports
                </a>
            </div>
        @endrole

        @role(['School Admin'])
            <!-- Settings -->
            <div class="pt-2">
                <a href="{{ $schoolSlugForSidebar ? route('admin.settings.index', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            </div>
        @endrole

        @role(['Parent'])
            <!-- Parent Section -->
            <div class="pt-4 mt-4 border-t border-gray-700">
                <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">My Account</p>
                
                @php
    // Centralized school slug helper
    function getSchoolSlug() {
        if (request()->route('school')) {
            return request()->route('school');
        }
        
        $user = auth()->user();
        if ($user) {
            // For parents, get from guardian
            if ($user->hasRole('Parent') && $user->guardian_id) {
                $guardian = \App\Models\Guardian::find($user->guardian_id);
                if ($guardian && $guardian->school) {
                    return $guardian->school->slug;
                }
            }
            // For other roles, get from school_id
            elseif ($user->school_id) {
                $school = \App\Models\School::find($user->school_id);
                if ($school) {
                    return $school->slug;
                }
            }
        }
        
        return null;
    }
    
    $schoolSlug = getSchoolSlug();
@endphp
                
                <a href="{{ $schoolSlug ? route('admin.parent.children', ['school' => $schoolSlug]) : '#' }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.parent.children') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    My Children
                </a>

                <a href="{{ $schoolSlugForSidebar ? route('admin.payments.mine', ['school' => $schoolSlugForSidebar]) : '#' }}" 
                   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.payments.mine') ? 'bg-indigo-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    My Payments
                </a>
            </div>
        @endrole
    </nav>
</aside>
