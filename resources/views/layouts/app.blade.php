<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" x-effect="document.body.classList.toggle('overflow-hidden', sidebarOpen)" x-on:keydown.escape.window="sidebarOpen = false" class="min-h-screen bg-gray-100 flex overflow-x-hidden">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Mobile Backdrop -->
            <div 
                x-show="sidebarOpen" 
                x-transition.opacity 
                class="fixed inset-0 bg-black/40 md:hidden z-20"
                @click="sidebarOpen = false"
                aria-hidden="true"></div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col md:ml-72 ml-0">
                <!-- Top Header -->
                <header class="bg-white shadow sticky top-0 z-10">
                    <div class="py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <!-- Mobile menu button -->
                            <button @click="sidebarOpen = !sidebarOpen" aria-label="Toggle Sidebar" :aria-expanded="sidebarOpen.toString()" class="md:hidden inline-flex items-center p-2 rounded text-gray-600 hover:text-gray-800 hover:bg-gray-100 focus:outline-none">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="3" y1="12" x2="21" y2="12" />
                                    <line x1="3" y1="6" x2="21" y2="6" />
                                    <line x1="3" y1="18" x2="21" y2="18" />
                                </svg>
                            </button>
                            <h2 class="font-semibold text-xl text-gray-800">
                            @isset($header)
                                {{ $header }}
                            @else
                                {{ __('Dashboard') }}
                            @endisset
                            </h2>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
