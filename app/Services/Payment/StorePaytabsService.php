<?php

namespace App\Services\Payment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StorePaytabsService extends PaytabsService
{
    /**
     * تهيئة المتغيرات
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * تهيئة بيانات العميل لاستخدامها في الدفع
     *
     * @param array $customerData
     * @return array
     */
    public function prepareCustomerDetails(array $customerData): array
    {
        return [
            'name' => $customerData['name'] ?? '',
            'email' => $customerData['email'] ?? '',
            'phone' => $customerData['phone'] ?? '',
            'address' => $customerData['address'] ?? '',
            'city' => $customerData['city'] ?? '',
            'state' => $customerData['state'] ?? '',
            'country' => $customerData['country'] ?? 'SA'
        ];
    }

    /**
     * إنشاء طلب دفع جديد
     *
     * @param array $orderData
     * @param float $amount
     * @param array $customerData
     * @return array
     */
    public function createPaymentRequest(array $orderData, float $amount, array $customerData): array
    {
        $reference = [
            'transaction' => $orderData['payment_id'],
            'order' => $orderData['payment_id']
        ];

        $returnUrl = config('app.url') . '/store/payment/callback';
        $callbackUrl = config('app.url') . '/api/store/payment/webhook';

        $payload = [
            'profile_id' => $this->profileId,
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            'cart_id' => $orderData['payment_id'],
            'cart_description' => $orderData['description'] ?? 'Order Payment',
            'cart_currency' => $this->currency,
            'cart_amount' => $amount,
            'hide_shipping' => true,
            'callback' => $callbackUrl,
            'return' => $returnUrl,
            'customer_details' => [
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'street1' => $customerData['address'],
                'city' => $customerData['city'],
                'state' => $customerData['state'],
                'country' => $customerData['country'],
                'zip' => $orderData['zip'] ?? '00000'
            ],
            'shipping_details' => [
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'street1' => $customerData['address'],
                'city' => $customerData['city'],
                'state' => $customerData['state'],
                'country' => $customerData['country'],
                'zip' => $orderData['zip'] ?? '00000'
            ],
            'user_defined' => [
                'custom1' => 'store_order',
                'custom2' => json_encode($reference),
                'udf9' => 'store_order'
            ]
        ];

        // تسجيل الطلب قبل الإرسال
        Log::info('Creating PayTabs store payment request', [
            'order_id' => $orderData['payment_id'],
            'amount' => $amount,
            'payload' => $payload
        ]);

        try {
            // إرسال الطلب لبوابة الدفع
            $response = $this->sendRequest('payment/request', $payload);

            if (!empty($response['redirect_url'])) {
                Log::info('PayTabs store payment request successful', [
                    'order_id' => $orderData['payment_id'],
                    'tran_ref' => $response['tran_ref'] ?? null,
                    'redirect_url' => $response['redirect_url']
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'tran_ref' => $response['tran_ref'] ?? null,
                        'redirect_url' => $response['redirect_url']
                    ]
                ];
            }

            // في حالة الخطأ
            Log::error('PayTabs store payment request failed', [
                'order_id' => $orderData['payment_id'],
                'response' => $response
            ]);

            return [
                'success' => false,
                'error' => [
                    'message' => $response['message'] ?? 'Failed to connect to payment gateway',
                    'details' => $response
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Exception during PayTabs store payment request', [
                'order_id' => $orderData['payment_id'],
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'message' => 'Payment gateway connection failed: ' . $e->getMessage(),
                    'details' => []
                ]
            ];
        }
    }

    /**
     * استخراج بيانات الدفع من طلب callback
     *
     * @param Request $request
     * @return array
     */
    public function extractPaymentData(Request $request): array
    {
        $data = $request->all();

        $tranRef = $data['tran_ref'] ?? $request->query('tranRef', '');
        $cartId = $data['cart_id'] ?? '';
        $respCode = $data['respcode'] ?? '';
        $respStatus = $data['respstatus'] ?? '';
        $respMessage = $data['respmsg'] ?? '';
        $amount = $data['cart_amount'] ?? 0;

        Log::info('PayTabs store payment callback received', [
            'tran_ref' => $tranRef,
            'cart_id' => $cartId,
            'resp_code' => $respCode,
            'resp_status' => $respStatus,
            'data' => $data
        ]);

        $udf2 = $data['user_defined']['udf2'] ?? null;

        if ($udf2) {
            $reference = json_decode($udf2, true);
            $paymentId = $reference['order'] ?? null;
        } else {
            $paymentId = $cartId;
        }

        return [
            'tranRef' => $tranRef,
            'paymentId' => $paymentId,
            'cartId' => $cartId,
            'amount' => (float) $amount,
            'isSuccessful' => $respStatus === 'A' || $respStatus === 'H',
            'isPending' => $respStatus === 'P',
            'responseCode' => $respCode,
            'responseStatus' => $respStatus,
            'responseMessage' => $respMessage,
            'rawData' => $data
        ];
    }

    /**
     * التحقق من حالة الدفع
     *
     * @param array $paymentData
     * @return array
     */
    public function verifyPaymentStatus(array $paymentData): array
    {
        if (empty($paymentData['tranRef'])) {
            return $paymentData;
        }

        try {
            $payload = [
                'profile_id' => $this->profileId,
                'tran_ref' => $paymentData['tranRef']
            ];

            Log::info('Verifying PayTabs store payment status', [
                'tran_ref' => $paymentData['tranRef'],
                'payment_id' => $paymentData['paymentId'] ?? ''
            ]);

            $response = $this->sendRequest('payment/query', $payload);

            Log::info('PayTabs store payment verification response', [
                'tran_ref' => $paymentData['tranRef'],
                'payment_id' => $paymentData['paymentId'] ?? '',
                'response' => $response
            ]);

            // تحديث بيانات الدفع
            if (!empty($response['tran_ref'])) {
                $paymentData['responseCode'] = $response['respcode'] ?? $paymentData['responseCode'];
                $paymentData['responseStatus'] = $response['respstatus'] ?? $paymentData['responseStatus'];
                $paymentData['responseMessage'] = $response['respmsg'] ?? $paymentData['responseMessage'];
                $paymentData['amount'] = !empty($response['cart_amount']) ? (float) $response['cart_amount'] : $paymentData['amount'];

                // التحقق من حالة الدفع
                $paymentData['isSuccessful'] = $response['respstatus'] === 'A' || $response['respstatus'] === 'H';
                $paymentData['isPending'] = $response['respstatus'] === 'P';

                $paymentData['rawData'] = array_merge($paymentData['rawData'] ?? [], $response);
            }
        } catch (\Exception $e) {
            Log::error('Exception during PayTabs store payment verification', [
                'tran_ref' => $paymentData['tranRef'],
                'payment_id' => $paymentData['paymentId'] ?? '',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $paymentData;
    }
}
