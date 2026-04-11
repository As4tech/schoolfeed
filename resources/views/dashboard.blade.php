<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('School Feeding Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">{{ __("Welcome to School Feeding Manager!") }}</p>
                    <p class="text-sm text-gray-600">{{ __("Manage student meal programs, track payments, and monitor cafeteria operations efficiently.") }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
