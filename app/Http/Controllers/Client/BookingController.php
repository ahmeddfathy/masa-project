<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageAddon;

use App\Services\Booking\BookingService;
use App\Services\Booking\AvailabilityService;
use App\Services\Booking\PaymentService;
use App\Services\Payment\PaytabsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Notifications\BookingStatusUpdated;

class BookingController extends Controller
{
    protected $bookingService;
    protected $availabilityService;
    protected $paymentService;
    protected $paytabsService;

    public function __construct(
        BookingService $bookingService,
        AvailabilityService $availabilityService,
        PaymentService $paymentService ,
        PaytabsService $paytabsService
    )
    {
        $this->bookingService = $bookingService;
        $this->availabilityService = $availabilityService;
        $this->paymentService = $paymentService;
        $this->paytabsService = $paytabsService;
    }

    /**
     * عرض صفحة الحجز الرئيسية
     * تعرض الخدمات النشطة والباقات والإضافات والحجوزات الحالية
     */
    public function index(Request $request)
    {
        // جلب كل البيانات اللازمة لصفحة الحجز
        $data = $this->bookingService->getBookingPageData();

        // استعادة البيانات القديمة من الجلسة (إذا كانت موجودة)
        if ($oldData = session('booking_form_data')) {
            foreach ($oldData as $key => $value) {
                session()->flash("_old_input.{$key}", $value);
            }
            session()->forget('booking_form_data');
        }

        return view('client.booking.index', $data);
    }

    /**
     * إنشاء حجز جديد
     * يتضمن التحقق من صحة البيانات والتحقق من توفر الموعد
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'package_id' => 'required|exists:packages,id',
            'session_date' => 'required|date|after:today',
            'session_time' => 'required',
            'baby_name' => 'nullable|string|max:100',
            'baby_birth_date' => 'nullable|date',
            'gender' => 'nullable|in:ذكر,أنثى',
            'notes' => 'nullable|string',
            'addons' => 'nullable|array',
            'image_consent' => 'required|in:0,1',
            'terms_consent' => 'required|accepted'
        ]);

        $package = Package::findOrFail($validated['package_id']);

        // التحقق من تعارضات المواعيد
        if ($this->availabilityService->checkBookingConflicts(
            $validated['session_date'],
            $validated['session_time'],
            $package
        )) {
            // البحث عن أقرب موعد متاح
            $nextAvailable = $this->availabilityService->getNextAvailableSlot(
                $package,
                $validated['session_date']
            );

            $message = 'عذراً، هذا الموعد محجوز بالفعل.';
            if ($nextAvailable) {
                $firstSlot = $nextAvailable['slots'][0] ?? null;
                if ($firstSlot) {
                    $message .= sprintf(
                        ' أقرب موعد متاح هو يوم %s الساعة %s',
                        Carbon::parse($nextAvailable['date'])->translatedFormat('l j F Y'),
                        $firstSlot['formatted_time']
                    );
                }
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $message);
        }

        try {
            // حساب التكلفة الإجمالية وإنشاء الحجز
            $totalAmount = $this->bookingService->calculateTotalAmount($package, $validated['addons'] ?? []);

            // إذا كان هناك نظام دفع مفعل
            if ($this->paymentService) {
                $addons = [];
                if (!empty($validated['addons'])) {
                    foreach ($validated['addons'] as $addonData) {
                        if (isset($addonData['id'])) {
                            $addon = PackageAddon::findOrFail($addonData['id']);
                            $quantity = $addonData['quantity'] ?? 1;
                            $addons[] = [
                                'id' => $addon->id,
                                'quantity' => $quantity,
                                'price' => $addon->price
                            ];
                        }
                    }
                }

                $payment_id = 'PAY-' . strtoupper(Str::random(8)) . '-' . time();

                $bookingData = array_merge($validated, [
                    'user_id' => Auth::id(),
                    'total_amount' => $totalAmount,
                    'addons' => $addons,
                    'payment_id' => $payment_id,
                    'package_name' => $package->name,
                    'uuid' => (string) Str::uuid()
                ]);
                session(['pending_booking' => $bookingData]);

                $user = Auth::user();

                // إنشاء طلب دفع باستخدام خدمة الدفع
                $paymentResult = $this->paymentService->initiatePayment($bookingData, $totalAmount, $user);

                if ($paymentResult['success'] && !empty($paymentResult['redirect_url'])) {
                    session(['payment_transaction_id' => $paymentResult['transaction_id']]);
                    return redirect($paymentResult['redirect_url']);
                }

                // في حالة فشل إنشاء طلب الدفع
                session(['booking_form_data' => $request->all()]);
                return redirect()->route('client.bookings.create')
                    ->with('error', 'فشل الاتصال ببوابة الدفع: ' . ($paymentResult['message'] ?? 'خطأ غير معروف'));
            }

            // الإنشاء المباشر إذا لم يكن هناك نظام دفع
            $validated['uuid'] = (string) Str::uuid();
            $booking = $this->bookingService->createBooking($validated, $totalAmount, Auth::id());

            return redirect()->route('client.bookings.success', $booking->uuid)
                ->with('success', 'تم إنشاء الحجز بنجاح!');

        } catch (\Exception $e) {
            Log::error('Error creating booking: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء إنشاء الحجز. الرجاء المحاولة مرة أخرى.');
        }
    }

    /**
     * معالجة استجابة الدفع من بوابة الدفع
     */
    public function paymentCallback(Request $request)
    {
        if (!$this->paymentService) {
            abort(404);
        }

        // معالجة استجابة الدفع
        $paymentData = $this->paymentService->processPaymentResponse($request);

        // التحقق من بيانات الحجز المعلقة
        $bookingData = session('pending_booking');
        if (!$bookingData) {
            return redirect()->route('client.bookings.create')
                ->with('error', 'خطأ في الدفع - لم يتم العثور على بيانات الحجز');
        }

        // البحث عن الحجز الموجود
        $existingBooking = $this->paymentService->findExistingBooking($paymentData);

        if ($existingBooking) {
            // تحديث حالة الحجز الموجود
            $this->paymentService->updateBookingPaymentStatus($existingBooking, $paymentData);

            session()->forget(['pending_booking', 'payment_transaction_id']);
            return redirect()->route('client.bookings.success', $existingBooking->uuid)
                ->with('success', 'تم تأكيد الدفع بنجاح!');
        }

        // التعامل مع فشل الدفع
        if (!$paymentData['isSuccessful'] && !$paymentData['isPending']) {
            session()->forget(['pending_booking', 'payment_transaction_id']);
            return redirect()->route('client.bookings.create')
                ->with('error', 'فشل الدفع: ' . ($paymentData['message'] ?: 'خطأ غير معروف'));
        }

        // إنشاء حجز جديد
        try {
            $booking = $this->paymentService->createBookingFromPayment($bookingData, $paymentData);

            session()->forget(['pending_booking', 'payment_transaction_id']);

            $message = $paymentData['isSuccessful'] ?
                'تم تأكيد الدفع بنجاح!' :
                'تم إنشاء الحجز، جارٍ التحقق من حالة الدفع...';

            return redirect()->route('client.bookings.success', $booking->uuid)
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Error creating booking from payment: ' . $e->getMessage(), [
                'exception' => $e,
                'payment_data' => $paymentData,
                'booking_data' => $bookingData
            ]);

            return redirect()->route('client.bookings.create')
                ->with('error', 'عذراً، حدث خطأ أثناء إنشاء الحجز. الرجاء الاتصال بالدعم الفني.');
        }
    }

    /**
     * إعادة توجيه المستخدم من بوابة الدفع
     */
    public function paymentReturn(Request $request)
    {
        return $this->paymentCallback($request);
    }

    /**
     * عرض صفحة نجاح الحجز
     */
    public function success(Booking $booking)
    {
        // التحقق من ملكية الحجز
        if ($booking->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'غير مصرح لك بعرض هذا الحجز');
        }

        return view('client.booking.success', compact('booking'));
    }

    /**
     * عرض قائمة حجوزات المستخدم
     */
    public function myBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['service', 'package', 'addons'])
            ->latest()
            ->paginate(10);

        return view('client.booking.my-bookings', compact('bookings'));
    }

    /**
     * عرض تفاصيل حجز معين
     */
    public function show(Booking $booking)
    {
        // التحقق من ملكية الحجز
        if ($booking->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'غير مصرح لك بعرض هذا الحجز');
        }

        $booking->load(['service', 'package', 'addons']);
        return view('client.booking.show', compact('booking'));
    }

    /**
     * حفظ بيانات نموذج الحجز في الجلسة
     * يستخدم عند الحاجة للتسجيل قبل إكمال الحجز
     */
    public function saveFormData(Request $request)
    {
        $formData = $request->all();
        $formData['image_consent'] = $request->input('image_consent', '0');
        $formData['terms_consent'] = $request->has('terms_consent');

        session(['booking_form_data' => $formData]);

        return redirect()->route($request->query('redirect', 'register'));
    }

    /**
     * إعادة محاولة الدفع لحجز موجود
     * يستخدم عندما يفشل الدفع أو يكون الحجز في حالة انتظار الدفع
     */
    public function retryPayment(Booking $booking)
    {
        // التحقق من ملكية الحجز
        if ($booking->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'غير مصرح لك بتعديل هذا الحجز');
        }

        // التحقق من أن الحجز في حالة تسمح بإعادة الدفع
        if (!in_array($booking->status, ['pending', 'payment_failed', 'payment_required'])) {
            return redirect()->route('client.bookings.show', $booking->uuid)
                ->with('error', 'لا يمكن إعادة الدفع لهذا الحجز في حالته الحالية');
        }

        try {
            $user = Auth::user();
            $payment_id = $booking->payment_id ?? 'PAY-' . strtoupper(Str::random(8)) . '-' . time();

            // تجهيز بيانات الحجز لإعادة الدفع
            $bookingData = [
                'uuid' => $booking->uuid,
                'user_id' => $booking->user_id,
                'service_id' => $booking->service_id,
                'package_id' => $booking->package_id,
                'session_date' => $booking->session_date->format('Y-m-d'),
                'session_time' => $booking->session_time->format('H:i'),
                'payment_id' => $payment_id
            ];

            // إنشاء طلب دفع جديد
            $paymentResult = $this->paymentService->initiatePayment($bookingData, $booking->total_amount, $user);

            if ($paymentResult['success'] && !empty($paymentResult['redirect_url'])) {
                // تحديث معرف الدفع في الحجز
                $booking->payment_id = $payment_id;
                $booking->save();

                session(['payment_transaction_id' => $paymentResult['transaction_id']]);
                return redirect($paymentResult['redirect_url']);
            }

            // في حالة فشل إنشاء طلب الدفع
            return redirect()->route('client.bookings.show', $booking->uuid)
                ->with('error', 'فشل الاتصال ببوابة الدفع: ' . ($paymentResult['message'] ?? 'خطأ غير معروف'));

        } catch (\Exception $e) {
            Log::error('Error retrying payment: ' . $e->getMessage(), [
                'exception' => $e,
                'booking_id' => $booking->id
            ]);

            return redirect()->route('client.bookings.show', $booking->uuid)
                ->with('error', 'حدث خطأ أثناء معالجة طلب الدفع. الرجاء المحاولة مرة أخرى لاحقًا.');
        }
    }

    /**
     * الحصول على المواعيد المتاحة ليوم معين
     */
    public function getAvailableTimeSlots(Request $request)
    {
        try {
            \Log::info('Getting available time slots', [
                'request_data' => $request->all()
            ]);

            $validated = $request->validate([
                'date' => 'required|date|after:today',
                'package_id' => 'required|exists:packages,id'
            ]);

            \Log::info('Validation passed', [
                'validated_data' => $validated
            ]);

            $package = Package::findOrFail($validated['package_id']);
            $date = Carbon::createFromFormat('Y-m-d', $validated['date'])->startOfDay();

            \Log::info('Found package and parsed date', [
                'package' => $package->toArray(),
                'date' => $date->format('Y-m-d')
            ]);

            $slots = $this->availabilityService->getAvailableTimeSlotsForDate($date, $package);

            \Log::info('Retrieved available slots', [
                'slots_count' => count($slots),
                'slots' => $slots
            ]);

            return response()->json([
                'status' => 'success',
                'slots' => $slots,
                'message' => null
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getAvailableTimeSlots: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء جلب المواعيد المتاحة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث حالة الحجز
     */
    public function updateStatus(Booking $booking, Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,cancelled,completed,no_show,rescheduled',
                'notes' => 'nullable|string'
            ]);

            // تحديث حالة الحجز
            $booking->status = $validated['status'];
            if (isset($validated['notes'])) {
                $booking->notes = $validated['notes'];
            }
            $booking->save();

            // إرسال إشعار للمستخدم
            $booking->user->notify(new BookingStatusUpdated($booking));

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث حالة الحجز بنجاح'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating booking status: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تحديث حالة الحجز'
            ], 500);
        }
    }
}
