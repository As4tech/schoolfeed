<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default School Settings
    |--------------------------------------------------------------------------
    |
    | These are the default settings for all schools. When a new school is
    | created, these values will be used unless overridden.
    |
    */

    'defaults' => [
        // General Settings
        'general' => [
            'school_name' => [
                'value' => 'My School',
                'type' => 'string',
                'label' => 'School Name',
                'description' => 'The official name of your school',
            ],
            'logo' => [
                'value' => null,
                'type' => 'string',
                'label' => 'School Logo',
                'description' => 'Upload your school logo',
            ],
            'contact_email' => [
                'value' => 'admin@school.com',
                'type' => 'string',
                'label' => 'Contact Email',
                'description' => 'Official contact email for the school',
            ],
            'phone' => [
                'value' => '+2348000000000',
                'type' => 'string',
                'label' => 'Phone Number',
                'description' => 'Official contact phone number',
            ],
            'address' => [
                'value' => '123 School Street, City, Country',
                'type' => 'string',
                'label' => 'Address',
                'description' => 'School physical address',
            ],
        ],

        // Payment Settings
        'payment' => [
            'paystack_subaccount_code' => [
                'value' => '',
                'type' => 'string',
                'label' => 'Paystack Subaccount Code',
                'description' => 'Your Paystack subaccount code for payments',
            ],
            'platform_fee_percentage' => [
                'value' => 1,
                'type' => 'float',
                'label' => 'Platform Fee (%)',
                'description' => 'Percentage fee charged by the platform',
            ],
            'payment_methods' => [
                'value' => ['momo', 'card'],
                'type' => 'array',
                'label' => 'Accepted Payment Methods',
                'description' => 'Select which payment methods to accept',
                'options' => [
                    'momo' => 'Mobile Money',
                    'card' => 'Credit/Debit Card',
                    'bank' => 'Bank Transfer',
                ],
            ],
        ],

        // Feeding Settings
        'feeding' => [
            'default_feeding_fee' => [
                'value' => 5000,
                'type' => 'integer',
                'label' => 'Default Feeding Fee',
                'description' => 'Default amount for feeding plans',
            ],
            'feeding_type' => [
                'value' => 'daily',
                'type' => 'string',
                'label' => 'Feeding Type',
                'description' => 'How feeding fees are charged',
                'options' => [
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'termly' => 'Termly',
                ],
            ],
            'allow_unpaid_feeding' => [
                'value' => false,
                'type' => 'boolean',
                'label' => 'Allow Unpaid Feeding',
                'description' => 'Allow students to feed even with unpaid fees',
            ],
        ],

        // Notification Settings
        'notification' => [
            'enable_sms' => [
                'value' => true,
                'type' => 'boolean',
                'label' => 'Enable SMS Notifications',
                'description' => 'Send notifications via SMS',
            ],
            'enable_email' => [
                'value' => true,
                'type' => 'boolean',
                'label' => 'Enable Email Notifications',
                'description' => 'Send notifications via email',
            ],
            'reminder_frequency' => [
                'value' => 'weekly',
                'type' => 'string',
                'label' => 'Payment Reminder Frequency',
                'description' => 'How often to send payment reminders',
                'options' => [
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                ],
            ],
        ],
    ],
];
