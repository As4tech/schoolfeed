<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('School Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">{{ $school->name }}</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('superadmin.schools.edit', ['managed_school' => $school]) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Edit
                            </a>
                            <a href="{{ route('superadmin.schools.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">School Information</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Name:</span>
                                    <p class="text-gray-900">{{ $school->name }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Email:</span>
                                    <p class="text-gray-900">{{ $school->email }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Phone:</span>
                                    <p class="text-gray-900">{{ $school->phone ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Status:</span>
                                    @if($school->isActive())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Payment Integration</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Paystack Subaccount:</span>
                                    <p class="text-gray-900">{{ $school->paystack_subaccount_code ?? 'Not configured' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Address</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-900">{{ $school->address ?? 'No address provided' }}</p>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 uppercase mb-2">Users ({{ $school->users->count() }})</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                @if($school->users->count() > 0)
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($school->users as $user)
                                            <li class="py-2 flex justify-between items-center">
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                                </div>
                                                <span class="text-xs text-gray-500">{{ $user->roles->pluck('name')->first() }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500">No users assigned to this school yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
