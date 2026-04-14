<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            School Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.settings.update', ['school' => request()->route('school')]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div x-data="{ activeTab: 'general' }" class="bg-white shadow rounded-lg">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                            <button type="button" @click="activeTab = 'general'" 
                                    :class="activeTab === 'general' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm">
                                General
                            </button>
                            <button type="button" @click="activeTab = 'payment'" 
                                    :class="activeTab === 'payment' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm">
                                Payment
                            </button>
                            <button type="button" @click="activeTab = 'feeding'" 
                                    :class="activeTab === 'feeding' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm">
                                Feeding
                            </button>
                            <button type="button" @click="activeTab = 'notification'" 
                                    :class="activeTab === 'notification' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm">
                                Notifications
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="p-6">
                        <!-- General Settings Tab -->
                        <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <form method="POST" action="{{ route('admin.settings.update', ['school' => request()->route('school')]) }}" enctype="multipart/form-data" class="space-y-6">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="tab" value="general">
                                <h3 class="text-lg font-medium text-gray-900">General Settings</h3>
                                
                                <!-- School Name -->
                                <div>
                                    <label for="general_school_name" class="block text-sm font-medium text-gray-700">
                                        {{ $generalSettings['school_name']['label'] }}
                                    </label>
                                    <input type="text" id="general_school_name" name="general[school_name]" 
                                           value="{{ $generalSettings['school_name']['value'] }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <p class="mt-1 text-sm text-gray-500">{{ $generalSettings['school_name']['description'] }}</p>
                                </div>

                                <!-- Logo -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ $generalSettings['logo']['label'] }}
                                    </label>
                                    @if($generalSettings['logo']['value'])
                                        <div class="mt-2 flex items-center space-x-4">
                                            <img src="{{ Storage::url($generalSettings['logo']['value']) }}" alt="School Logo" class="h-20 w-20 object-cover rounded">
                                            <button type="button" onclick="if(confirm('Are you sure you want to remove the logo?')) { window.location.href='{{ route('admin.settings.remove-logo', ['school' => request()->route('school')]) }}'; }"
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                Remove Logo
                                            </button>
                                        </div>
                                    @endif
                                    <input type="file" id="general_logo" name="general[logo]" 
                                           accept="image/*"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-1 text-sm text-gray-500">{{ $generalSettings['logo']['description'] }}</p>
                                </div>

                                <!-- Contact Email -->
                                <div>
                                    <label for="general_contact_email" class="block text-sm font-medium text-gray-700">
                                        {{ $generalSettings['contact_email']['label'] }}
                                    </label>
                                    <input type="email" id="general_contact_email" name="general[contact_email]" 
                                           value="{{ $generalSettings['contact_email']['value'] }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <p class="mt-1 text-sm text-gray-500">{{ $generalSettings['contact_email']['description'] }}</p>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="general_phone" class="block text-sm font-medium text-gray-700">
                                        {{ $generalSettings['phone']['label'] }}
                                    </label>
                                    <input type="tel" id="general_phone" name="general[phone]" 
                                           value="{{ $generalSettings['phone']['value'] }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <p class="mt-1 text-sm text-gray-500">{{ $generalSettings['phone']['description'] }}</p>
                                </div>

                                <!-- Address -->
                                <div>
                                    <label for="general_address" class="block text-sm font-medium text-gray-700">
                                        {{ $generalSettings['address']['label'] }}
                                    </label>
                                    <textarea id="general_address" name="general[address]" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $generalSettings['address']['value'] }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">{{ $generalSettings['address']['description'] }}</p>
                                </div>

                                <!-- Currency -->
                                <div>
                                    <label for="general_currency" class="block text-sm font-medium text-gray-700">
                                        {{ $generalSettings['currency']['label'] }}
                                    </label>
                                    <select id="general_currency" name="general[currency]" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="GHS" {{ $generalSettings['currency']['value'] === 'GHS' ? 'selected' : '' }}>GHS - Ghana Cedi</option>
                                        <option value="USD" {{ $generalSettings['currency']['value'] === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                        <option value="NGN" {{ $generalSettings['currency']['value'] === 'NGN' ? 'selected' : '' }}>NGN - Nigerian Naira</option>
                                        <option value="EUR" {{ $generalSettings['currency']['value'] === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="GBP" {{ $generalSettings['currency']['value'] === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">{{ $generalSettings['currency']['description'] }}</p>
                                </div>

                                <!-- Save Button -->
                                <div class="flex justify-end pt-4 border-t border-gray-200">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Save General Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Payment Settings Tab -->
                        <div x-show="activeTab === 'payment'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <form method="POST" action="{{ route('admin.settings.update', ['school' => request()->route('school')]) }}" class="space-y-6">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="tab" value="payment">
                                <h3 class="text-lg font-medium text-gray-900">Payment Settings</h3>
                                
                                <!-- Paystack Subaccount Code -->
                                <div>
                                    <label for="payment_paystack_subaccount_code" class="block text-sm font-medium text-gray-700">
                                        {{ $paymentSettings['paystack_subaccount_code']['label'] }}
                                    </label>
                                    <input type="text" id="payment_paystack_subaccount_code" name="payment[paystack_subaccount_code]" 
                                           value="{{ $paymentSettings['paystack_subaccount_code']['value'] }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <p class="mt-1 text-sm text-gray-500">{{ $paymentSettings['paystack_subaccount_code']['description'] }}</p>
                                </div>

                                <!-- Platform Fee -->
                                <div>
                                    <label for="payment_platform_fee_percentage" class="block text-sm font-medium text-gray-700">
                                        {{ $paymentSettings['platform_fee_percentage']['label'] }}
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="number" id="payment_platform_fee_percentage" name="payment[platform_fee_percentage]" 
                                               value="{{ $paymentSettings['platform_fee_percentage']['value'] }}"
                                               step="0.01" min="0" max="100"
                                               class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ $paymentSettings['platform_fee_percentage']['description'] }}</p>
                                </div>

                                <!-- Payment Methods -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ $paymentSettings['payment_methods']['label'] }}
                                    </label>
                                    <div class="mt-2 space-y-2">
                                        @foreach($paymentSettings['payment_methods']['options'] as $value => $label)
                                            <div class="flex items-center">
                                                <input type="checkbox" id="payment_methods_{{ $value }}" name="payment[payment_methods][]" 
                                                       value="{{ $value }}" 
                                                       @if(in_array($value, $paymentSettings['payment_methods']['value'])) checked @endif
                                                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <label for="payment_methods_{{ $value }}" class="ml-2 block text-sm text-gray-900">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ $paymentSettings['payment_methods']['description'] }}</p>
                                </div>

                                <!-- Save Button -->
                                <div class="flex justify-end pt-4 border-t border-gray-200">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Save Payment Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Feeding Settings Tab -->
                        <div x-show="activeTab === 'feeding'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <form method="POST" action="{{ route('admin.settings.update', ['school' => request()->route('school')]) }}" class="space-y-6">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="tab" value="feeding">
                                <h3 class="text-lg font-medium text-gray-900">Feeding Settings</h3>
                                
                                <!-- Default Feeding Fee -->
                                <div>
                                    <label for="feeding_default_feeding_fee" class="block text-sm font-medium text-gray-700">
                                        {{ $feedingSettings['default_feeding_fee']['label'] }}
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">GHS </span>
                                        </div>
                                        <input type="number" id="feeding_default_feeding_fee" name="feeding[default_feeding_fee]" 
                                               value="{{ $feedingSettings['default_feeding_fee']['value'] }}"
                                               min="0"
                                               class="block w-full pl-12 pr-12 rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ $feedingSettings['default_feeding_fee']['description'] }}</p>
                                </div>

                                <!-- Feeding Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ $feedingSettings['feeding_type']['label'] }}
                                    </label>
                                    <select id="feeding_feededing_type" name="feeding[feeding_type]" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @foreach($feedingSettings['feeding_type']['options'] as $value => $label)
                                            <option value="{{ $value }}" @if($feedingSettings['feeding_type']['value'] == $value) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">{{ $feedingSettings['feeding_type']['description'] }}</p>
                                </div>

                                <!-- Allow Unpaid Feeding -->
                                <div>
                                    <div class="flex items-center">
                                        <input type="hidden" name="feeding[allow_unpaid_feeding]" value="0">
                                        <input type="checkbox" id="feeding_allow_unpaid_feeding" name="feeding[allow_unpaid_feeding]" 
                                               value="1"
                                               @if($feedingSettings['allow_unpaid_feeding']['value']) checked @endif
                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label for="feeding_allow_unpaid_feeding" class="ml-2 block text-sm text-gray-900">
                                            {{ $feedingSettings['allow_unpaid_feeding']['label'] }}
                                        </label>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ $feedingSettings['allow_unpaid_feeding']['description'] }}</p>
                                </div>

                                <!-- Save Button -->
                                <div class="flex justify-end pt-4 border-t border-gray-200">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Save Feeding Settings
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Notification Settings Tab -->
                        <div x-show="activeTab === 'notification'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            <form method="POST" action="{{ route('admin.settings.update', ['school' => request()->route('school')]) }}" class="space-y-6">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="tab" value="notification">
                                <h3 class="text-lg font-medium text-gray-900">Notification Settings</h3>
                                
                                <!-- Enable SMS -->
                                <div>
                                    <div class="flex items-center">
                                        <input type="hidden" name="notification[enable_sms]" value="0">
                                        <input type="checkbox" id="notification_enable_sms" name="notification[enable_sms]" 
                                               value="1"
                                               @if($notificationSettings['enable_sms']['value']) checked @endif
                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label for="notification_enable_sms" class="ml-2 block text-sm text-gray-900">
                                            {{ $notificationSettings['enable_sms']['label'] }}
                                        </label>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ $notificationSettings['enable_sms']['description'] }}</p>
                                </div>

                                <!-- Enable Email -->
                                <div>
                                    <div class="flex items-center">
                                        <input type="hidden" name="notification[enable_email]" value="0">
                                        <input type="checkbox" id="notification_enable_email" name="notification[enable_email]" 
                                               value="1"
                                               @if($notificationSettings['enable_email']['value']) checked @endif
                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <label for="notification_enable_email" class="ml-2 block text-sm text-gray-900">
                                            {{ $notificationSettings['enable_email']['label'] }}
                                        </label>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ $notificationSettings['enable_email']['description'] }}</p>
                                </div>

                                <!-- Reminder Frequency -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ $notificationSettings['reminder_frequency']['label'] }}
                                    </label>
                                    <select id="notification_reminder_frequency" name="notification[reminder_frequency]" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @foreach($notificationSettings['reminder_frequency']['options'] as $value => $label)
                                            <option value="{{ $value }}" @if($notificationSettings['reminder_frequency']['value'] == $value) selected @endif>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">{{ $notificationSettings['reminder_frequency']['description'] }}</p>
                                </div>

                                <!-- Save Button -->
                                <div class="flex justify-end pt-4 border-t border-gray-200">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Save Notification Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </form>
        </div>
    </div>
</x-app-layout>
