<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookingTabbyService
{
    protected $apiUrl;
    protected $secretKey;
    protected $publicKey;
    protected $merchantCode;

    public function __construct()
    {
        $this->apiUrl = 'https://api.tabby.ai';
        $this->secretKey = config('services.tabby.secret_key');
        $this->publicKey = config('services.tabby.public_key');
        $this->merchantCode = config('services.tabby.merchant_code');
    }

    public function prepareCustomerDetails(array $customerData): array
    {
        $names = explode(' ', $customerData['name'], 2);
        return [
            'first_name' => $names[0] ?? '',
            'last_name' => $names[1] ?? '',
            'phone' => $customerData['phone'],
            'email' => $customerData['email'],
        ];
    }

    public function createPaymentRequest(array $bookingData, float $amount, array $customerData): array
    {
        try {
            Log::info('Starting Tabby booking payment request', [
                'booking_id' => $bookingData['payment_id'],
                'amount' => $amount
            ]);

            $items = [];
            if (!empty($bookingData['addons'])) {
                foreach ($bookingData['addons'] as $addon) {
                    $items[] = [
                        'title' => 'Addon #' . $addon['id'],
                        'description' => 'Quantity: ' . $addon['quantity'],
                        'quantity' => $addon['quantity'],
                        'unit_price' => $addon['price'],
                        'reference_id' => (string)$addon['id'],
                        'category' => 'service'
                    ];
                }
            }

            $items[] = [
                'title' => $bookingData['package_name'] ?? 'Package Booking',
                'description' => 'Session on ' . $bookingData['session_date'],
                'quantity' => 1,
                'unit_price' => $amount - (isset($bookingData['addons_total']) ? $bookingData['addons_total'] : 0),
                'reference_id' => 'package-' . ($bookingData['package_id'] ?? 'unknown'),
                'category' => 'service'
            ];

            $testCredentials = config('services.tabby.is_sandbox');

            $phone = $customerData['phone'];
            $email = $customerData['email'];

            if ($testCredentials) {
                if (session()->has('tabby_test_phone')) {
                    $phone = session('tabby_test_phone');
                    session()->forget('tabby_test_phone');
                } else {
                    $phone = '+966500000001';
                }

                Log::info('Using real user email in test mode for Tabby', [
                    'email' => $email,
                    'phone' => $phone
                ]);
            }

            $payload = [
                "payment" => [
                    "amount" => $amount,
                    "currency" => "SAR",
                    "description" => $bookingData['description'] ?? 'Booking #' . $bookingData['payment_id'],
                    "buyer" => [
                        "phone" => $phone,
                        "email" => $email,
                        "name" => $customerData['first_name'] . ' ' . $customerData['last_name']
                    ],
                    "shipping_address" => [
                        "city" => $customerData['city'] ?? 'Riyadh',
                        "address" => $bookingData['shipping_address'] ?? $customerData['address'] ?? 'N/A',
                        "zip" => "00000",
                    ],
                    "order" => [
                        "tax_amount" => "0.00",
                        "shipping_amount" => "0.00",
                        "discount_amount" => "0.00",
                        "updated_at" => now()->toIso8601String(),
                        "reference_id" => $bookingData['payment_id'],
                        "items" => $items
                    ],
                    "buyer_history" => [
                        "registered_since" => now()->subDays(30)->toIso8601String(),
                        "loyalty_level" => 0,
                    ],
                ],
                "lang" => "ar",
                "merchant_code" => $this->merchantCode,
                "merchant_urls" => [
                    "success" => route('client.bookings.payment.return') . '?payment_id=' . $bookingData['payment_id'],
                    "cancel" => route('client.bookings.create'),
                    "failure" => route('client.bookings.create'),
                ]
            ];

            Log::info('Booking Tabby request payload', ['payload' => $payload]);

            $response = Http::withToken($this->publicKey)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($this->apiUrl . '/api/v2/checkout', $payload);

            $statusCode = $response->status();
            $responseBody = $response->body();
            $responseData = $response->json() ?: [];

            Log::info('Booking Tabby API raw response', [
                'status' => $statusCode,
                'body' => $responseBody,
                'parsed' => $responseData
            ]);

            if ($response->successful()) {
                if (!empty($responseData['id'])) {
                    $redirectUrl = null;

                    if (isset($responseData['configuration']) &&
                        isset($responseData['configuration']['available_products']) &&
                        isset($responseData['configuration']['available_products']['installments']) &&
                        isset($responseData['configuration']['available_products']['installments'][0]['web_url'])) {
                        $redirectUrl = $responseData['configuration']['available_products']['installments'][0]['web_url'];
                    }

                    if (!$redirectUrl && isset($responseData['checkout_url'])) {
                        $redirectUrl = $responseData['checkout_url'];
                    }

                    if ($redirectUrl) {
                        Log::info('Booking Tabby payment created successfully', [
                            'id' => $responseData['id'],
                            'redirect_url' => $redirectUrl
                        ]);

                        return [
                            'success' => true,
                            'data' => [
                                'tran_ref' => $responseData['id'],
                                'redirect_url' => $redirectUrl
                            ]
                        ];
                    }
                }

                Log::warning('Booking Tabby response missing expected fields', ['response' => $responseData]);
                return [
                    'success' => false,
                    'error' => [
                        'message' => 'Invalid response format from Tabby',
                        'details' => $responseData
                    ]
                ];
            }

            $errorMsg = $responseData['message'] ?? 'Unknown error';
            $logLevel = 'error';
            $userMessage = '';

            switch ($statusCode) {
                case 400:
                    $userMessage = 'Invalid request format: ' . $errorMsg;
                    break;
                case 401:
                    $userMessage = 'Authentication failed. Please check your Tabby API keys.';
                    break;
                case 403:
                    $userMessage = 'Access forbidden. Your API key may not have the correct permissions.';
                    break;
                case 404:
                    $userMessage = 'API endpoint not found. Please check the API URL and endpoint path.';
                    break;
                case 405:
                    $userMessage = 'Method not allowed. Please check the API endpoint and method.';
                    break;
                case 422:
                    $userMessage = 'Validation failed: ' . $errorMsg;
                    break;
                case 429:
                    $userMessage = 'Too many requests. Please try again later.';
                    break;
                case 500:
                case 502:
                case 503:
                case 504:
                    $userMessage = 'Tabby service is currently unavailable. Please try again later.';
                    break;
                default:
                    $userMessage = 'Failed to connect to Tabby: ' . $errorMsg;
            }

            Log::$logLevel('Booking Tabby API error', [
                'status' => $statusCode,
                'message' => $errorMsg,
                'response' => $responseData
            ]);

            return [
                'success' => false,
                'error' => [
                    'message' => $userMessage,
                    'details' => $responseData,
                    'status_code' => $statusCode
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Exception in Booking Tabby payment request', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'message' => 'Error processing Booking Tabby payment: ' . $e->getMessage()
                ]
            ];
        }
    }

    public function extractPaymentData(Request $request): array
    {
        $paymentId = $request->get('payment_id');
        Log::info('Extracting booking payment data', ['payment_id' => $paymentId, 'request' => $request->all()]);

        if (empty($paymentId)) {
            return [
                'isSuccessful' => false,
                'isPending' => false,
                'tranRef' => null,
                'paymentId' => null,
                'amount' => 0,
                'message' => 'No payment ID received'
            ];
        }

        if (config('services.tabby.is_sandbox')) {
            return [
                'tranRef' => $paymentId,
                'paymentId' => $request->get('merchant_reference_id'),
                'amount' => 400,
                'isPending' => false,
                'isSuccessful' => true,
                'message' => 'Payment successful (Test Mode)'
            ];
        }

        return [
            'tranRef' => $paymentId,
            'paymentId' => $request->get('merchant_reference_id'),
            'amount' => 0,
            'isPending' => true,
            'isSuccessful' => false,
            'message' => 'Payment status pending verification'
        ];
    }

    public function verifyPaymentStatus(array $paymentData): array
    {
        try {
            if (empty($paymentData['tranRef'])) {
                return array_merge($paymentData, [
                    'isSuccessful' => false,
                    'isPending' => false,
                    'message' => 'No transaction reference to verify'
                ]);
            }

            if (config('services.tabby.is_sandbox') && $paymentData['isSuccessful']) {
                Log::info('Booking payment already verified as successful in test mode', ['tranRef' => $paymentData['tranRef']]);
                return $paymentData;
            }

            Log::info('Verifying Booking Tabby payment status', ['tranRef' => $paymentData['tranRef']]);

            $response = Http::withToken($this->secretKey)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->get($this->apiUrl . '/api/v2/checkout/' . $paymentData['tranRef']);

            $statusCode = $response->status();
            $responseBody = $response->body();
            $responseData = $response->json() ?: [];

            Log::info('Booking Tabby payment verification response', [
                'status' => $statusCode,
                'body' => $responseBody,
                'parsed' => $responseData
            ]);

            if (!$response->successful()) {
                if (config('services.tabby.is_sandbox')) {
                    Log::info('Booking Tabby verification failed in sandbox mode, assuming success', [
                        'status' => $statusCode,
                        'response' => $responseData
                    ]);

                    return array_merge($paymentData, [
                        'isSuccessful' => true,
                        'isPending' => false,
                        'message' => 'Payment successful (Test Mode)'
                    ]);
                }

                Log::error('Booking Tabby payment verification failed', [
                    'status' => $statusCode,
                    'response' => $responseData
                ]);

                return array_merge($paymentData, [
                    'isSuccessful' => false,
                    'isPending' => false,
                    'message' => 'Failed to verify payment: ' . ($responseData['message'] ?? 'Unknown error')
                ]);
            }

            $amount = 0;
            if (isset($responseData['payment']) && isset($responseData['payment']['amount'])) {
                $amount = $responseData['payment']['amount'];
            }

            $status = $responseData['payment']['status'] ?? 'UNKNOWN';
            Log::info('Booking Tabby payment status', ['status' => $status, 'amount' => $amount]);

            $isSuccessful = in_array($status, ['AUTHORIZED', 'CLOSED', 'CAPTURED', 'COMPLETED']);
            $isPending = in_array($status, ['CREATED', 'PENDING']);

            return array_merge($paymentData, [
                'isSuccessful' => $isSuccessful,
                'isPending' => $isPending,
                'amount' => $amount,
                'message' => $isSuccessful ? 'Payment successful' : ($isPending ? 'Payment pending' : 'Payment failed: ' . $status)
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in Booking Tabby payment verification', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return array_merge($paymentData, [
                'isSuccessful' => false,
                'isPending' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage()
            ]);
        }
    }
}
