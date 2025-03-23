<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\User;
use App\Services\Payment\PaytabsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * خدمة باي تابز للدفع
     */
    protected $paytabsService;

    /**
     * إنشاء كائن جديد من خدمة الدفع
     */
    public function __construct(PaytabsService $paytabsService)
    {
        $this->paytabsService = $paytabsService;
    }

    /**
     * تحضير بيانات الدفع وإنشاء طلب دفع جديد
     *
     * @param array $bookingData بيانات الحجز
     * @param float $amount المبلغ المطلوب دفعه
     * @param User $user بيانات المستخدم
     * @return array نتائج عملية إنشاء طلب الدفع
     */
    public function initiatePayment(array $bookingData, float $amount, User $user): array
    {
        // توليد معرف دفع فريد
        $paymentId = $bookingData['payment_id'] ?? 'PAY-' . strtoupper(Str::random(8)) . '-' . time();
        $bookingData['payment_id'] = $paymentId;

        // تحضير بيانات العميل
        $customerData = $this->paytabsService->prepareCustomerDetails([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address ?? null,
            'city' => $user->city ?? null,
            'state' => $user->state ?? null,
            'country' => 'EG'
        ]);

        // إنشاء وصف للمعاملة
        if (isset($bookingData['package_name'])) {
            $bookingData['description'] = 'Photography Session - ' . $bookingData['package_name'];
        }

        // إنشاء طلب دفع جديد عبر باي تابز
        $response = $this->paytabsService->createPaymentRequest($bookingData, $amount, $customerData);

        if (!empty($response['success']) && !empty($response['data']['redirect_url'])) {
            // حفظ معرف المعاملة في الجلسة
            session(['payment_transaction_id' => $response['data']['tran_ref'] ?? null]);

            return [
                'success' => true,
                'redirect_url' => $response['data']['redirect_url'],
                'transaction_id' => $response['data']['tran_ref'] ?? null,
                'payment_id' => $paymentId
            ];
        }

        // إعداد رسالة الخطأ
        $error = $response['error'] ?? ['message' => 'فشل الاتصال ببوابة الدفع'];
        Log::error('فشل في بدء عملية الدفع', [
            'booking_data' => $bookingData,
            'amount' => $amount,
            'user_id' => $user->id,
            'error' => $error
        ]);

        return [
            'success' => false,
            'message' => $error['message'] ?? 'فشل الاتصال ببوابة الدفع',
            'error' => $error
        ];
    }

    /**
     * معالجة استجابة الدفع من بوابة الدفع
     *
     * @param Request $request الطلب الحالي
     * @return array نتائج معالجة الدفع
     */
    public function processPaymentResponse(Request $request): array
    {
        // استخراج بيانات الدفع
        $paymentData = $this->paytabsService->extractPaymentData($request);

        // التحقق من حالة الدفع
        if ($paymentData['tranRef']) {
            $paymentData = $this->paytabsService->verifyPaymentStatus($paymentData);
        }

        return $paymentData;
    }

    /**
     * البحث عن حجز موجود باستخدام معرف الدفع أو رقم المعاملة
     *
     * @param array $paymentData بيانات الدفع
     * @return Booking|null الحجز الموجود أو null
     */
    public function findExistingBooking(array $paymentData): ?Booking
    {
        if (empty($paymentData['tranRef']) && empty($paymentData['paymentId'])) {
            return null;
        }

        return Booking::where(function($query) use ($paymentData) {
            if (!empty($paymentData['tranRef'])) {
                $query->where('payment_transaction_id', $paymentData['tranRef']);
            }
            if (!empty($paymentData['paymentId'])) {
                $query->orWhere('payment_id', $paymentData['paymentId']);
            }
        })->first();
    }

    /**
     * تحديث حالة الحجز بناءً على نتيجة الدفع
     *
     * @param Booking $booking الحجز
     * @param array $paymentData بيانات الدفع
     * @return Booking الحجز المحدث
     */
    public function updateBookingPaymentStatus(Booking $booking, array $paymentData): Booking
    {
        if ($paymentData['isSuccessful'] && $booking->status !== 'confirmed') {
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => $paymentData['status'],
                'payment_transaction_id' => $paymentData['tranRef'] ?? $booking->payment_transaction_id
            ]);
        } elseif ($paymentData['isPending'] && $booking->status !== 'confirmed') {
            $booking->update([
                'status' => 'pending',
                'payment_status' => $paymentData['status'],
                'payment_transaction_id' => $paymentData['tranRef'] ?? $booking->payment_transaction_id
            ]);
        } elseif (!$paymentData['isSuccessful'] && !$paymentData['isPending']) {
            $booking->update([
                'status' => 'failed',
                'payment_status' => $paymentData['status'],
                'payment_transaction_id' => $paymentData['tranRef'] ?? $booking->payment_transaction_id
            ]);
        }

        return $booking;
    }

    /**
     * إنشاء حجز جديد بناءً على بيانات الدفع
     *
     * @param array $bookingData بيانات الحجز
     * @param array $paymentData بيانات الدفع
     * @return Booking الحجز الجديد
     */
    public function createBookingFromPayment(array $bookingData, array $paymentData): Booking
    {
        $status = $paymentData['isSuccessful'] ? 'confirmed' :
                 ($paymentData['isPending'] ? 'pending' : 'failed');

        $bookingParams = array_merge($bookingData, [
            'payment_transaction_id' => $paymentData['tranRef'] ?? null,
            'payment_id' => $paymentData['paymentId'] ?? $bookingData['payment_id'] ?? null,
            'payment_status' => $paymentData['status'] ?? 'unknown',
            'status' => $status,
            'booking_date' => now()
        ]);

        // إزالة البيانات غير المطلوبة
        $addons = $bookingParams['addons'] ?? [];
        unset($bookingParams['addons']);

        // إنشاء الحجز
        $booking = Booking::create($bookingParams);

        // إضافة الإضافات
        if (!empty($addons)) {
            foreach ($addons as $addon) {
                if (isset($addon['id'])) {
                    $booking->addons()->attach($addon['id'], [
                        'quantity' => $addon['quantity'] ?? 1,
                        'price_at_booking' => $addon['price'] ?? 0
                    ]);
                }
            }
        }

        return $booking;
    }
}
