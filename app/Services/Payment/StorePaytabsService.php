<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StorePaytabsService
{
    /**
     * PayTabs Profile ID
     */
    protected $profileId;

    /**
     * PayTabs Server Key
     */
    protected $serverKey;

    /**
     * Default Currency
     */
    protected $currency;

    /**
     * Sandbox Mode
     */
    protected $isSandbox;

    /**
     * Maximum number of retries
     */
    protected $maxRetries = 3;

    /**
     * PayTabs API URL
     */
    protected $apiUrl = 'https://secure-egypt.paytabs.com';

    /**
     * Create a new PayTabs service instance
     */
    public function __construct()
    {
        $this->profileId = config('services.paytabs.profile_id');
        $this->serverKey = config('services.paytabs.server_key');
        $this->currency = config('services.paytabs.currency', 'SAR');
        $this->isSandbox = config('services.paytabs.is_sandbox', true);
    }

    /**
     * Create a new payment request for a store order
     *
     * @param array $orderData Order data
     * @param float $amount Payment amount
     * @param array $customerData Customer details
     * @return array Payment API response
     */
    public function createPaymentRequest(array $orderData, float $amount, array $customerData): array
    {
        $paymentId = $orderData['payment_id'];
        $description = $orderData['description'] ?? 'Store Order Payment';

        $paymentData = [
            "profile_id" => $this->profileId,
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => $paymentId,
            "cart_description" => $description,
            "cart_currency" => $this->currency,
            "cart_amount" => $amount,
            "callback" => route('checkout.payment.callback'),
            "return" => route('checkout.payment.return'),
            "customer_details" => $customerData,
            "hide_shipping" => true,
            "framed" => true,
            "is_sandbox" => $this->isSandbox,
            "is_hosted" => true
        ];

        return $this->sendRequest('/payment/request', $paymentData);
    }

    /**
     * Query a transaction status
     *
     * @param string $tranRef Transaction reference
     * @return array Transaction query response
     */
    public function queryTransaction(string $tranRef): array
    {
        $data = [
            'profile_id' => $this->profileId,
            'tran_ref' => $tranRef
        ];

        return $this->sendRequest('/payment/query', $data);
    }

    /**
     * Send request to PayTabs API
     *
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array API response
     */
    private function sendRequest(string $endpoint, array $data): array
    {
        $attempt = 1;
        $lastError = null;

        while ($attempt <= $this->maxRetries) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => $this->serverKey,
                        'Content-Type' => 'application/json'
                    ])
                    ->withOptions([
                        'verify' => !app()->environment('local'),
                        'connect_timeout' => 30
                    ])
                    ->post($this->apiUrl . $endpoint, $data);

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'data' => $response->json() ?? [],
                        'status_code' => $response->status()
                    ];
                }

                $lastError = [
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ];
            } catch (\Exception $e) {
                $lastError = [
                    'message' => $e->getMessage(),
                    'type' => get_class($e)
                ];

                Log::error('PayTabs API Error', [
                    'endpoint' => $endpoint,
                    'error' => $lastError,
                    'attempt' => $attempt
                ]);
            }

            if ($attempt < $this->maxRetries) {
                sleep(pow(2, $attempt - 1)); // Exponential backoff
            }
            $attempt++;
        }

        return [
            'success' => false,
            'error' => $lastError,
            'message' => 'فشل الاتصال بخدمة الدفع بعد عدة محاولات'
        ];
    }

    /**
     * Extract payment data from the request
     *
     * @param Request $request Current request
     * @return array Payment data
     */
    public function extractPaymentData(Request $request): array
    {
        $paymentData = [
            'status' => $request->input('respStatus') ?? $request->input('status'),
            'tranRef' => $request->input('tran_ref') ?? session('payment_transaction_id'),
            'paymentId' => $request->input('cart_id') ?? session('pending_order.payment_id'),
            'message' => $request->input('respMessage') ?? $request->input('message'),
            'amount' => null,
            'currency' => $this->currency
        ];

        return $paymentData;
    }

    /**
     * Verify payment status and update data
     *
     * @param array $paymentData Initial payment data
     * @return array Updated payment data
     */
    public function verifyPaymentStatus(array $paymentData): array
    {
        if (!empty($paymentData['tranRef'])) {
            $queryResponse = $this->queryTransaction($paymentData['tranRef']);

            if (!empty($queryResponse['success']) && !empty($queryResponse['data'])) {
                $result = $queryResponse['data'];
                $paymentData['status'] = $result['payment_result']['response_status'] ?? $paymentData['status'];
                $paymentData['message'] = $result['payment_result']['response_message'] ?? $paymentData['message'];
                $paymentData['amount'] = $result['tran_total'] ?? null;
                $paymentData['currency'] = $result['tran_currency'] ?? $this->currency;
            }
        }

        $paymentData['isSuccessful'] = $this->isSuccessfulStatus($paymentData['status']);
        $paymentData['isPending'] = $this->isPendingStatus($paymentData['status']);

        return $paymentData;
    }

    /**
     * Check if payment status is successful
     *
     * @param string|null $status Payment status
     * @return bool
     */
    private function isSuccessfulStatus($status): bool
    {
        $successStatuses = [
            'A', 'H', 'P', 'V', 'success', 'SUCCESS', '1', 1, 'CAPTURED',
            '100', 'Authorised', 'Captured', 'Approved'
        ];

        return in_array($status, $successStatuses, true);
    }

    /**
     * Check if payment status is pending
     *
     * @param string|null $status Payment status
     * @return bool
     */
    private function isPendingStatus($status): bool
    {
        $pendingStatuses = [
            'PENDING', 'pending', 'H', 'P', '2', 'PROCESSING'
        ];

        return in_array($status, $pendingStatuses, true);
    }

    /**
     * Prepare customer details for payment
     *
     * @param array $user User data
     * @return array Formatted customer data
     */
    public function prepareCustomerDetails(array $user): array
    {
        // Extract user data or set defaults
        $name = $user['name'] ?? '';
        $email = $user['email'] ?? '';
        $phone = $user['phone'] ?? '';

        // Split name into first and last name if available
        $nameParts = explode(' ', $name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Prepare address details
        $address = $user['address'] ?? '';
        $city = $user['city'] ?? '';
        $state = $user['state'] ?? '';
        $country = $user['country'] ?? 'SA'; // Default to Saudi Arabia

        return [
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "street1" => $address,
            "city" => $city,
            "state" => $state,
            "country" => $country,
            "zip" => $user['zip'] ?? '',
            "ip" => request()->ip()
        ];
    }
}
