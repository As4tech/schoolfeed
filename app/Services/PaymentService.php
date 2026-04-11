<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\School;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private ?string $baseUrl;
    private ?string $secretKey;
    private ?string $publicKey;

    public function __construct()
    {
        $this->baseUrl = config('services.paystack.base_url', 'https://api.paystack.co');
        $this->secretKey = config('services.paystack.secret_key') ?? '';
        $this->publicKey = config('services.paystack.public_key') ?? '';
    }

    /**
     * Initialize a Paystack transaction with subaccount
     *
     * @param array $data
     * @return array
     */
    public function initialize(array $data): array
    {
        try {
            $amountInPesewas = $data['amount'] * 100; // Convert to pesewas
            $platformFeeInPesewas = $data['platform_fee'] * 100; // 1% in pesewas

            $payload = [
                'email' => $data['email'],
                'amount' => $amountInPesewas,
                'reference' => $data['reference'],
                'callback_url' => $data['callback_url'],
                'metadata' => [
                    'payment_id' => $data['payment_id'],
                    'guardian_id' => $data['guardian_id'],
                    'school_id' => $data['school_id'],
                    'students' => $data['students'],
                    'custom_fields' => [
                        [
                            'display_name' => 'School',
                            'variable_name' => 'school_name',
                            'value' => $data['school_name'],
                        ],
                        [
                            'display_name' => 'Number of Students',
                            'variable_name' => 'student_count',
                            'value' => count($data['students']),
                        ],
                    ],
                ],
            ];

            // Add subaccount if school has one configured
            if (!empty($data['subaccount_code'])) {
                $payload['subaccount'] = $data['subaccount_code'];
                // The transaction_charge is what Paystack keeps as platform fee
                // School receives: amount - transaction_charge
                $payload['transaction_charge'] = $platformFeeInPesewas;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('Paystack transaction initialized', [
                    'reference' => $data['reference'],
                    'amount' => $data['amount'],
                    'response' => $result,
                ]);

                return [
                    'success' => true,
                    'authorization_url' => $result['data']['authorization_url'],
                    'access_code' => $result['data']['access_code'],
                    'reference' => $result['data']['reference'],
                ];
            }

            Log::error('Paystack initialization failed', [
                'reference' => $data['reference'],
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to initialize payment',
            ];

        } catch (\Exception $e) {
            Log::error('Paystack initialization exception', [
                'reference' => $data['reference'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment initialization failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a Paystack transaction
     *
     * @param string $reference
     * @return array
     */
    public function verify(string $reference): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            if ($response->successful()) {
                $result = $response->json();
                $data = $result['data'];

                Log::info('Paystack transaction verified', [
                    'reference' => $reference,
                    'status' => $data['status'],
                ]);

                return [
                    'success' => true,
                    'status' => $data['status'], // success, failed, pending
                    'amount' => $data['amount'] / 100, // Convert from pesewas
                    'reference' => $data['reference'],
                    'gateway_response' => $data['gateway_response'],
                    'paid_at' => $data['paid_at'] ?? null,
                    'channel' => $data['channel'] ?? null,
                    'fees' => ($data['fees'] ?? 0) / 100,
                    'metadata' => $data['metadata'] ?? [],
                ];
            }

            Log::error('Paystack verification failed', [
                'reference' => $reference,
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to verify payment',
            ];

        } catch (\Exception $e) {
            Log::error('Paystack verification exception', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create a subaccount for a school
     *
     * @param School $school
     * @param array $bankDetails
     * @return array
     */
    public function createSubaccount(School $school, array $bankDetails): array
    {
        try {
            $payload = [
                'business_name' => $school->name,
                'settlement_bank' => $bankDetails['bank_code'],
                'account_number' => $bankDetails['account_number'],
                'percentage_charge' => 0, // We handle fees manually via transaction_charge
                'description' => 'School feeding payment subaccount',
            ];

            if ($school->primary_contact_email) {
                $payload['primary_contact_email'] = $school->primary_contact_email;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/subaccount', $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('Paystack subaccount created', [
                    'school_id' => $school->id,
                    'subaccount_code' => $result['data']['subaccount_code'],
                ]);

                return [
                    'success' => true,
                    'subaccount_code' => $result['data']['subaccount_code'],
                    'account_name' => $result['data']['account_name'] ?? null,
                    'bank_name' => $result['data']['bank_name'] ?? null,
                ];
            }

            Log::error('Paystack subaccount creation failed', [
                'school_id' => $school->id,
                'response' => $response->json(),
            ]);

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Failed to create subaccount',
            ];

        } catch (\Exception $e) {
            Log::error('Paystack subaccount creation exception', [
                'school_id' => $school->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Subaccount creation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get list of supported banks
     *
     * @param string $country
     * @return array
     */
    public function getBanks(string $country = 'ghana'): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/bank', [
                'country' => $country,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'banks' => $response->json()['data'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch banks',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify account number
     *
     * @param string $accountNumber
     * @param string $bankCode
     * @return array
     */
    public function verifyAccountNumber(string $accountNumber, string $bankCode): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/bank/resolve', [
                'account_number' => $accountNumber,
                'bank_code' => $bankCode,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return [
                    'success' => true,
                    'account_name' => $result['data']['account_name'],
                    'account_number' => $result['data']['account_number'],
                ];
            }

            return [
                'success' => false,
                'message' => 'Invalid account number',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
