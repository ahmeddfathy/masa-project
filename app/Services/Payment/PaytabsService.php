<?php

namespace App\Services\Payment;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaytabsService
{
    /**
     * معرف الملف الشخصي لباي تابز
     */
    protected $profileId;

    /**
     * مفتاح الخادم لباي تابز
     */
    protected $serverKey;

    /**
     * العملة الافتراضية
     */
    protected $currency;

    /**
     * وضع الاختبار
     */
    protected $isSandbox;

    /**
     * عدد المحاولات الأقصى
     */
    protected $maxRetries = 3;

    /**
     * رابط API لباي تابز
     */
    protected $apiUrl = 'https://secure-egypt.paytabs.com';

    /**
     * إنشاء كائن جديد من خدمة باي تابز
     */
    public function __construct()
    {
        $this->profileId = config('services.paytabs.profile_id');
        $this->serverKey = config('services.paytabs.server_key');
        $this->currency = config('services.paytabs.currency');
        $this->isSandbox = config('services.paytabs.is_sandbox');
    }

    /**
     * إنشاء طلب دفع جديد
     *
     * @param array $bookingData بيانات الحجز
     * @param float $amount المبلغ المطلوب دفعه
     * @param array $customerData بيانات العميل
     * @return array استجابة بوابة الدفع
     */
    public function createPaymentRequest(array $bookingData, float $amount, array $customerData): array
    {
        $paymentId = $bookingData['payment_id'];
        $description = $bookingData['description'] ?? 'Photography Session Booking';

        $paymentData = [
            "profile_id" => $this->profileId,
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => $paymentId,
            "cart_description" => $description,
            "cart_currency" => $this->currency,
            "cart_amount" => $amount,
            "callback" => route('client.bookings.payment.callback'),
            "return" => route('client.bookings.payment.return'),
            "customer_details" => $customerData,
            "hide_shipping" => true,
            "framed" => true,
            "is_sandbox" => $this->isSandbox,
            "is_hosted" => true
        ];

        return $this->sendRequest('/payment/request', $paymentData);
    }

    /**
     * الاستعلام عن حالة معاملة
     *
     * @param string $tranRef مرجع المعاملة
     * @return array استجابة الاستعلام
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
     *
     * @param string $endpoint نقطة النهاية
     * @param array $data البيانات المرسلة
     * @return array استجابة الطلب
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
     * استخراج بيانات الدفع من الطلب
     *
     * @param Request $request الطلب الحالي
     * @return array بيانات الدفع
     */
    public function extractPaymentData(Request $request): array
    {
        $paymentData = [
            'status' => $request->input('respStatus') ?? $request->input('status'),
            'tranRef' => $request->input('tran_ref') ?? session('payment_transaction_id'),
            'paymentId' => $request->input('payment_id') ?? session('pending_booking.payment_id'),
            'message' => $request->input('respMessage') ?? $request->input('message'),
            'amount' => null,
            'currency' => $this->currency
        ];

        return $paymentData;
    }

    /**
     * التحقق من حالة الدفع وتحديث البيانات
     *
     * @param array $paymentData بيانات الدفع الأولية
     * @return array بيانات الدفع المحدثة
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
     * التحقق إذا كانت حالة الدفع ناجحة
     *
     * @param string|null $status حالة الدفع
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
     * التحقق إذا كانت حالة الدفع معلقة
     *
     * @param string|null $status حالة الدفع
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
     * تحضير بيانات العميل للدفع
     *
     * @param array $user بيانات المستخدم
     * @return array بيانات العميل منسقة
     */
    public function prepareCustomerDetails(array $user): array
    {
        return [
            "name" => $user['name'] ?? 'Customer',
            "email" => $user['email'] ?? '',
            "phone" => $user['phone'] ?? '',
            "street1" => $user['address'] ?? 'Client Address',
            "city" => $user['city'] ?? 'City',
            "state" => $user['state'] ?? 'State',
            "country" => $user['country'] ?? 'EG',
            "zip" => $user['zip'] ?? '00000'
        ];
    }
}
