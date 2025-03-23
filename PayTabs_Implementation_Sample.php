<?php

/**
 * نموذج تنفيذ خدمة الدفع باستخدام باي تابز
 * Sample PayTabs Payment Implementation
 *
 * يعرض هذا الملف نموذجاً أساسياً لكيفية دمج خدمة الدفع PayTabs في مشروع جديد
 * This file demonstrates a basic implementation of PayTabs payment in a new project
 */

namespace App\Services\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaytabsService
{
    /**
     * معرف الملف الشخصي لباي تابز
     * PayTabs Profile ID
     */
    protected $profileId;

    /**
     * مفتاح الخادم لباي تابز
     * PayTabs Server Key
     */
    protected $serverKey;

    /**
     * العملة الافتراضية
     * Default Currency
     */
    protected $currency;

    /**
     * وضع الاختبار
     * Sandbox Mode
     */
    protected $isSandbox;

    /**
     * رابط API لباي تابز
     * PayTabs API URL
     */
    protected $apiUrl = 'https://secure-egypt.paytabs.com';

    /**
     * إنشاء كائن جديد من خدمة باي تابز
     * Create a new PayTabs service instance
     */
    public function __construct()
    {
        // Load configuration from config file or environment variables
        $this->profileId = config('services.paytabs.profile_id');
        $this->serverKey = config('services.paytabs.server_key');
        $this->currency = config('services.paytabs.currency', 'SAR');
        $this->isSandbox = config('services.paytabs.is_sandbox', true);
    }

    /**
     * إنشاء طلب دفع جديد
     * Create a new payment request
     *
     * @param string $paymentId Unique payment ID
     * @param string $description Payment description
     * @param float $amount Payment amount
     * @param array $customerData Customer details
     * @return array Payment response with redirect URL
     */
    public function createPaymentRequest(string $paymentId, string $description, float $amount, array $customerData): array
    {
        // Prepare payment data for PayTabs API
        $paymentData = [
            "profile_id" => $this->profileId,
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => $paymentId,
            "cart_description" => $description,
            "cart_currency" => $this->currency,
            "cart_amount" => $amount,
            "callback" => route('payment.callback'), // Your callback route
            "return" => route('payment.return'),     // Your return route
            "customer_details" => $customerData,
            "hide_shipping" => true,
            "framed" => true,
            "is_sandbox" => $this->isSandbox,
            "is_hosted" => true
        ];

        // Send request to PayTabs API
        return $this->sendRequest('/payment/request', $paymentData);
    }

    /**
     * الاستعلام عن حالة معاملة
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
     * إرسال طلب إلى واجهة برمجة تطبيقات باي تابز
     * Send request to PayTabs API
     *
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array API response
     */
    private function sendRequest(string $endpoint, array $data): array
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => $this->serverKey,
                    'Content-Type' => 'application/json'
                ])
                ->post($this->apiUrl . $endpoint, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json() ?? [],
                    'status_code' => $response->status()
                ];
            }

            $error = [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ];

            Log::error('PayTabs API Error', [
                'endpoint' => $endpoint,
                'error' => $error
            ]);

            return [
                'success' => false,
                'error' => $error,
                'message' => 'PayTabs API error: ' . $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('PayTabs API Exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'type' => get_class($e)
                ],
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * معالجة استجابة الدفع
     * Process payment response/callback
     *
     * @param Request $request The current request
     * @return array Processed payment data
     */
    public function processPaymentResponse(Request $request): array
    {
        // Extract payment data from request
        $paymentData = [
            'status' => $request->input('respStatus') ?? $request->input('status'),
            'tranRef' => $request->input('tran_ref'),
            'cartId' => $request->input('cart_id'),
            'message' => $request->input('respMessage') ?? $request->input('message')
        ];

        // Verify payment status if transaction reference exists
        if (!empty($paymentData['tranRef'])) {
            $queryResponse = $this->queryTransaction($paymentData['tranRef']);

            if (!empty($queryResponse['success']) && !empty($queryResponse['data'])) {
                $result = $queryResponse['data'];

                // Update payment data with verified information
                $paymentData['status'] = $result['payment_result']['response_status'] ?? $paymentData['status'];
                $paymentData['message'] = $result['payment_result']['response_message'] ?? $paymentData['message'];
                $paymentData['amount'] = $result['tran_total'] ?? null;
                $paymentData['currency'] = $result['tran_currency'] ?? $this->currency;
            }
        }

        // Determine if payment is successful or pending
        $paymentData['isSuccessful'] = $this->isSuccessfulStatus($paymentData['status']);
        $paymentData['isPending'] = $this->isPendingStatus($paymentData['status']);

        return $paymentData;
    }

    /**
     * التحقق إذا كانت حالة الدفع ناجحة
     * Check if payment status is successful
     *
     * @param string|null $status Payment status
     * @return bool Is successful
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
     * التحقق إذا كانت حالة الدفع معلقة
     * Check if payment status is pending
     *
     * @param string|null $status Payment status
     * @return bool Is pending
     */
    private function isPendingStatus($status): bool
    {
        $pendingStatuses = [
            'PENDING', 'pending', 'H', 'P', '2', 'PROCESSING'
        ];

        return in_array($status, $pendingStatuses, true);
    }

    /**
     * تحضير بيانات العميل للدفع
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

/**
 * مثال على كيفية استخدام الخدمة في متحكم
 * Example of how to use the service in a controller
 */
/*
namespace App\Http\Controllers;

use App\Services\Payment\PaytabsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paytabsService;

    public function __construct(PaytabsService $paytabsService)
    {
        $this->paytabsService = $paytabsService;
    }

    // Initiate payment
    public function initiatePayment(Request $request)
    {
        // Generate a unique payment ID
        $paymentId = 'PAY-' . strtoupper(Str::random(8)) . '-' . time();

        // Get amount from request
        $amount = $request->input('amount');

        // Prepare customer data
        $customerData = $this->paytabsService->prepareCustomerDetails([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'country' => 'SA'
        ]);

        // Create payment request
        $response = $this->paytabsService->createPaymentRequest(
            $paymentId,
            'Payment for Order',
            $amount,
            $customerData
        );

        // Store payment data in session
        if ($response['success'] && !empty($response['data']['redirect_url'])) {
            session(['payment_id' => $paymentId]);
            session(['payment_transaction_id' => $response['data']['tran_ref'] ?? null]);

            // Redirect to PayTabs payment page
            return redirect($response['data']['redirect_url']);
        }

        // Handle error
        return back()->with('error', 'Payment initialization failed: ' .
            ($response['message'] ?? 'Unknown error'));
    }

    // Payment callback
    public function paymentCallback(Request $request)
    {
        // Process payment response
        $paymentData = $this->paytabsService->processPaymentResponse($request);

        // Handle payment result
        if ($paymentData['isSuccessful']) {
            // Payment successful - update order status and redirect

            // Clear payment session data
            session()->forget(['payment_id', 'payment_transaction_id']);

            return redirect()->route('payment.success')->with('success', 'Payment successful!');
        } elseif ($paymentData['isPending']) {
            // Payment is still processing
            return redirect()->route('payment.pending')->with('info', 'Payment is processing...');
        } else {
            // Payment failed
            return redirect()->route('payment.failed')->with('error',
                'Payment failed: ' . ($paymentData['message'] ?? 'Unknown error'));
        }
    }

    // Payment return page (user redirect)
    public function paymentReturn(Request $request)
    {
        // Same logic as callback
        return $this->paymentCallback($request);
    }
}
*/

/**
 * مثال على تسجيل المسارات
 * Example routes registration
 */
/*
// In routes/web.php
Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback');
Route::get('/payment/return', [PaymentController::class, 'paymentReturn'])->name('payment.return');

Route::get('/payment/success', function() {
    return view('payment.success');
})->name('payment.success');

Route::get('/payment/failed', function() {
    return view('payment.failed');
})->name('payment.failed');

Route::get('/payment/pending', function() {
    return view('payment.pending');
})->name('payment.pending');
*/

/**
 * مثال لإعدادات باي تابز في ملف config/services.php
 * Example PayTabs configuration in config/services.php
 */
/*
// In config/services.php
'paytabs' => [
    'profile_id' => env('PAYTABS_PROFILE_ID'),
    'server_key' => env('PAYTABS_SERVER_KEY'),
    'currency' => env('PAYTABS_CURRENCY', 'SAR'),
    'is_sandbox' => env('PAYTABS_SANDBOX', true),
],
*/

/**
 * مثال لمتغيرات البيئة اللازمة
 * Example required environment variables
 */
/*
// In .env file
PAYTABS_PROFILE_ID=12345
PAYTABS_SERVER_KEY=SZDHFGJ79878KYUIHKJHG
PAYTABS_CURRENCY=SAR
PAYTABS_SANDBOX=true
*/
