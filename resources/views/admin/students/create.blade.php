<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Student') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.students.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700">Class *</label>
                                <select name="class_id" id="class_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Guardian Section -->
                            <div class="md:col-span-2 border-t pt-6 mt-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Parent/Guardian Information</h4>
                                
                                <div class="flex gap-4 mb-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="guardian_option" value="existing" 
                                            {{ old('guardian_option', 'existing') === 'existing' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600" onchange="toggleGuardianOption()">
                                        <span class="ml-2">Select Existing</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="guardian_option" value="new" 
                                            {{ old('guardian_option') === 'new' ? 'checked' : '' }}
                                            class="form-radio text-indigo-600" onchange="toggleGuardianOption()">
                                        <span class="ml-2">Create New</span>
                                    </label>
                                </div>
                                @error('guardian_option')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                <!-- Existing Guardian Select -->
                                <div id="existing-guardian" class="{{ old('guardian_option') === 'new' ? 'hidden' : '' }}">
                                    <label for="parent_id" class="block text-sm font-medium text-gray-700">Select Parent/Guardian</label>
                                    <select name="parent_id" id="parent_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Select --</option>
                                        @foreach($guardians as $guardian)
                                            <option value="{{ $guardian->id }}" {{ old('parent_id') == $guardian->id ? 'selected' : '' }}>
                                                {{ $guardian->name }} ({{ $guardian->phone }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parent_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- New Guardian Form -->
                                <div id="new-guardian" class="{{ old('guardian_option') !== 'new' ? 'hidden' : '' }} grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label for="guardian_name" class="block text-sm font-medium text-gray-700">Name *</label>
                                        <input type="text" name="guardian_name" id="guardian_name" value="{{ old('guardian_name') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('guardian_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="guardian_phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                                        <input type="text" name="guardian_phone" id="guardian_phone" value="{{ old('guardian_phone') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('guardian_phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="guardian_email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="guardian_email" id="guardian_email" value="{{ old('guardian_email') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('guardian_email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="guardian_occupation" class="block text-sm font-medium text-gray-700">Occupation</label>
                                        <input type="text" name="guardian_occupation" id="guardian_occupation" value="{{ old('guardian_occupation') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('guardian_occupation')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label for="guardian_address" class="block text-sm font-medium text-gray-700">Address</label>
                                        <textarea name="guardian_address" id="guardian_address" rows="2"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('guardian_address') }}</textarea>
                                        @error('guardian_address')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                <select name="gender" id="gender"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('emergency_contact_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('emergency_contact_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="allergies" class="block text-sm font-medium text-gray-700">Allergies</label>
                                <textarea name="allergies" id="allergies" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('allergies') }}</textarea>
                                @error('allergies')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="medical_notes" class="block text-sm font-medium text-gray-700">Medical Notes</label>
                                <textarea name="medical_notes" id="medical_notes" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('medical_notes') }}</textarea>
                                @error('medical_notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-4">
                            <a href="{{ route('admin.students.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Create Student
                            </button>
                        </div>
                    </form>

                    <script>
                        function toggleGuardianOption() {
                            const option = document.querySelector('input[name="guardian_option"]:checked').value;
                            const existingDiv = document.getElementById('existing-guardian');
                            const newDiv = document.getElementById('new-guardian');
                            
                            if (option === 'existing') {
                                existingDiv.classList.remove('hidden');
                                newDiv.classList.add('hidden');
                            } else {
                                existingDiv.classList.add('hidden');
                                newDiv.classList.remove('hidden');
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
