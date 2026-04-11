<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bulk Import Students') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Import Students from CSV</h3>
                        <a href="{{ route('admin.students.index') }}" class="text-gray-600 hover:text-gray-900">Back to Students</a>
                    </div>

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-blue-800 mb-2">CSV Format Instructions</h4>
                        <p class="text-sm text-blue-700 mb-2">Your CSV file must include these columns:</p>
                        <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
                            <li><strong>first_name</strong> - Required</li>
                            <li><strong>last_name</strong> - Required</li>
                            <li><strong>student_id</strong> - Required, must be unique</li>
                            <li><strong>grade</strong> - Required</li>
                            <li><strong>date_of_birth</strong> - Optional (YYYY-MM-DD format)</li>
                            <li><strong>gender</strong> - Optional (male/female/other)</li>
                            <li><strong>allergies</strong> - Optional</li>
                            <li><strong>emergency_contact_name</strong> - Optional</li>
                            <li><strong>emergency_contact_phone</strong> - Optional</li>
                            <li><strong>guardian_name</strong> - Optional</li>
                            <li><strong>guardian_phone</strong> - Optional (required for creating guardian)</li>
                            <li><strong>guardian_email</strong> - Optional</li>
                            <li><strong>guardian_address</strong> - Optional</li>
                            <li><strong>guardian_occupation</strong> - Optional</li>
                        </ul>
                        <p class="text-sm text-blue-700 mt-2">Guardians will be created automatically based on phone number if provided.</p>
                    </div>

                    <div class="mb-6">
                        <a href="{{ route('admin.students.template') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download Template CSV
                        </a>
                    </div>

                    <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">Upload CSV File</label>
                            <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('csv_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.students.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Import Students
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
