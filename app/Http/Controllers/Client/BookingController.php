<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageAddon;

use App\Services\Booking\BookingService;
use App\Services\Booking\AvailabilityService;
use App\Services\Booking\PaymentService;
use App\Services\Payment\StoreTabbyService;
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
    protected $tabbyService;

    public function __construct(
        BookingService $bookingService,
        AvailabilityService $availabilityService,
        PaymentService $paymentService,
        StoreTabbyService $tabbyService
    )
    {
        $this->bookingService = $bookingService;
        $this->availabilityService = $availabilityService;
        $this->paymentService = $paymentService;
        $this->tabbyService = $tabbyService;
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

        // استعادة بيانات الحجز المعلق إذا كانت موجودة في الجلسة (مثلا عند فشل الدفع)
        if ($pendingBooking = session('pending_booking')) {
            // نملأ البيانات القديمة بقيم من الحجز المعلق
            foreach ($pendingBooking as $key => $value) {
                if (!is_array($value)) {
                    session()->flash("_old_input.{$key}", $value);
                }
            }

            // تعيين حقول محددة بشكل صريح
            if (isset($pendingBooking['baby_name'])) {
                session()->flash('_old_input.baby_name', $pendingBooking['baby_name']);
            }

            if (isset($pendingBooking['coupon_code'])) {
                session()->flash('_old_input.coupon_code', $pendingBooking['coupon_code']);
            }

            if (isset($pendingBooking['package_id'])) {
                session()->flash('_old_input.package_id', $pendingBooking['package_id']);
            }

            if (isset($pendingBooking['session_time'])) {
                session()->flash('_old_input.session_time', $pendingBooking['session_time']);
            }

            // تعيين نوع الخدمة بشكل صريح
            if (isset($pendingBooking['service_id'])) {
                session()->flash('_old_input.service_id', $pendingBooking['service_id']);
            }

            // معالجة البيانات المركبة مثل الإضافات
            if (isset($pendingBooking['addons']) && is_array($pendingBooking['addons'])) {
                foreach ($pendingBooking['addons'] as $addon) {
                    if (isset($addon['id'])) {
                        $addonId = $addon['id'];
                        session()->flash("_old_input.addons.{$addonId}.id", $addonId);
                        if (isset($addon['quantity'])) {
                            session()->flash("_old_input.addons.{$addonId}.quantity", $addon['quantity']);
                        }
                    }
                }
            }
        }

        return view('client.booking.index', $data);
    }

    public function store(Request $request)
    {
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
            'coupon_code' => 'nullable|string',
            'image_consent' => 'required|in:0,1',
            'terms_consent' => 'required|accepted',
            'payment_method' => 'required|in:online,tabby,cod'
        ]);

        $package = Package::findOrFail($validated['package_id']);

        if ($this->availabilityService->checkBookingConflicts(
            $validated['session_date'],
            $validated['session_time'],
            $package
        )) {
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
            $totalAmount = $this->bookingService->calculateTotalAmount($package, $validated['addons'] ?? []);

            $coupon = null;
            $discountAmount = 0;

            if (!empty($validated['coupon_code'])) {
                $couponCode = strtoupper(trim($validated['coupon_code']));
                $coupon = \App\Models\Coupon::where('code', $couponCode)->first();

                if (!$coupon) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'كود الكوبون غير صالح');
                }

                if (!$coupon->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'كود الكوبون غير صالح أو منتهي الصلاحية');
                }

                if (!$coupon->appliesToPackage($package->id)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'هذا الكوبون لا ينطبق على الباقة المحددة');
                }

                // التحقق من استخدام الكوبون مسبقًا من قبل المستخدم الحالي
                if ($coupon->hasBeenUsedByCurrentUser('App\\Models\\Booking')) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'لقد قمت باستخدام هذا الكوبون مسبقًا');
                }

                if (floatval($totalAmount) < floatval($coupon->min_order_amount)) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', sprintf(
                            'الحد الأدنى للطلب لتطبيق هذا الكوبون هو %s ر.س',
                            number_format($coupon->min_order_amount, 2)
                        ));
                }

                $discountAmount = $coupon->calculateDiscount($totalAmount);
                $totalAmount -= $discountAmount;
            }

            // تعديل هنا: التحقق من طريقة الدفع
            if ($validated['payment_method'] === 'cod') {
                // إذا كان الدفع عند الاستلام، إنشاء الحجز مباشرة
                $booking = $this->bookingService->createBooking(
                    array_merge($validated, [
                        'payment_status' => 'pending',
                        'status' => 'pending',
                        'payment_method' => 'cod'
                    ]),
                    $totalAmount,
                    Auth::id(),
                    $discountAmount,
                    $coupon ? $coupon->id : null,
                    $coupon ? $coupon->code : null
                );

                if ($coupon) {
                    $coupon->recordUsageByUser(Auth::id(), $booking);
                }

                return redirect()->route('client.bookings.success', $booking->uuid)
                    ->with('success', 'تم إنشاء الحجز بنجاح! سيتم الدفع عند الحضور للجلسة.');
            }
            // تابي أو أي طريقة دفع أخرى، استمر باستخدام خدمة الدفع
            else if ($this->paymentService) {
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
                    'original_amount' => $this->bookingService->calculateTotalAmount($package, $validated['addons'] ?? []),
                    'discount_amount' => $discountAmount,
                    'coupon_id' => $coupon ? $coupon->id : null,
                    'coupon_code' => $coupon ? $coupon->code : null,
                    'addons' => $addons,
                    'payment_id' => $payment_id,
                    'package_name' => $package->name,
                    'uuid' => (string) Str::uuid()
                ]);
                session(['pending_booking' => $bookingData]);

                $user = Auth::user();

                $paymentResult = $this->paymentService->initiatePayment($bookingData, $totalAmount, $user);

                if ($paymentResult['success'] && !empty($paymentResult['redirect_url'])) {
                    session(['payment_transaction_id' => $paymentResult['transaction_id']]);
                    return redirect($paymentResult['redirect_url']);
                }

                session(['booking_form_data' => $request->all()]);
                return redirect()->route('client.bookings.create')
                    ->with('error', 'فشل الاتصال ببوابة الدفع: ' . ($paymentResult['message'] ?? 'خطأ غير معروف'));
            }

            $validated['uuid'] = (string) Str::uuid();
            $validated['coupon_id'] = $coupon ? $coupon->id : null;
            $validated['coupon_code'] = $coupon ? $coupon->code : null;
            $validated['discount_amount'] = $discountAmount;
            $booking = $this->bookingService->createBooking($validated, $totalAmount, Auth::id());

            if ($coupon) {
                $coupon->recordUsageByUser(Auth::id(), $booking);
            }

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

    public function paymentCallback(Request $request)
    {
        if (!$this->paymentService) {
            abort(404);
        }

        $paymentData = $this->paymentService->processPaymentResponse($request);

        $bookingData = session('pending_booking');
        if (!$bookingData) {
            return redirect()->route('client.bookings.create')
                ->with('error', 'خطأ في الدفع - لم يتم العثور على بيانات الحجز');
        }

        $existingBooking = $this->paymentService->findExistingBooking($paymentData);

        if ($existingBooking) {
            $this->paymentService->updateBookingPaymentStatus($existingBooking, $paymentData);

            session()->forget(['pending_booking', 'payment_transaction_id']);
            return redirect()->route('client.bookings.success', $existingBooking->uuid)
                ->with('success', 'تم تأكيد الدفع بنجاح!');
        }

        if (!$paymentData['isSuccessful'] && !$paymentData['isPending']) {
            return redirect()->route('client.bookings.create')
                ->with('error', 'فشل الدفع: ' . ($paymentData['message'] ?: 'خطأ غير معروف'))
                ->with('retry_payment', true);
        }

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
                ->with('error', 'عذراً، حدث خطأ أثناء إنشاء الحجز. الرجاء المحاولة مرة أخرى.')
                ->with('retry_payment', true);
        }
    }

    public function paymentReturn(Request $request)
    {
        return $this->paymentCallback($request);
    }

    public function success(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'غير مصرح لك بعرض هذا الحجز');
        }

        return view('client.booking.success', compact('booking'));
    }

    public function myBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['service', 'package', 'addons'])
            ->latest()
            ->paginate(10);

        return view('client.booking.my-bookings', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'غير مصرح لك بعرض هذا الحجز');
        }

        $booking->load(['service', 'package', 'addons']);
        return view('client.booking.show', compact('booking'));
    }

    public function saveFormData(Request $request)
    {
        $formData = $request->all();
        $formData['image_consent'] = $request->input('image_consent', '0');
        $formData['terms_consent'] = $request->has('terms_consent');

        session(['booking_form_data' => $formData]);

        return redirect()->route($request->query('redirect', 'register'));
    }

    public function retryPayment(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'غير مصرح لك بتعديل هذا الحجز');
        }

        if (!in_array($booking->status, ['pending', 'payment_failed', 'payment_required'])) {
            return redirect()->route('client.bookings.show', $booking->uuid)
                ->with('error', 'لا يمكن إعادة الدفع لهذا الحجز في حالته الحالية');
        }

        try {
            $user = Auth::user();
            $payment_id = $booking->payment_id ?? 'PAY-' . strtoupper(Str::random(8)) . '-' . time();

            $bookingData = [
                'uuid' => $booking->uuid,
                'user_id' => $booking->user_id,
                'service_id' => $booking->service_id,
                'package_id' => $booking->package_id,
                'session_date' => $booking->session_date->format('Y-m-d'),
                'session_time' => $booking->session_time->format('H:i'),
                'payment_id' => $payment_id
            ];

            $paymentResult = $this->paymentService->initiatePayment($bookingData, $booking->total_amount, $user);

            if ($paymentResult['success'] && !empty($paymentResult['redirect_url'])) {
                $booking->payment_id = $payment_id;
                $booking->save();

                session(['payment_transaction_id' => $paymentResult['transaction_id']]);
                return redirect($paymentResult['redirect_url']);
            }

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

    public function updateStatus(Booking $booking, Request $request)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,completed,cancelled',
                'notes' => 'nullable|string'
            ]);

            $booking->status = $validated['status'];
            if (isset($validated['notes'])) {
                $booking->notes = $validated['notes'];
            }
            $booking->save();

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

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'package_id' => 'required|exists:packages,id'
        ]);

        $couponCode = strtoupper(trim($request->coupon_code));
        $packageId = $request->package_id;

        $coupon = \App\Models\Coupon::where('code', $couponCode)->first();

        if (!$coupon) {
            return response()->json([
                'status' => 'error',
                'message' => 'كود الخصم غير صالح'
            ]);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'status' => 'error',
                'message' => 'كود الخصم منتهي الصلاحية أو تم استخدامه بالكامل'
            ]);
        }

        if (!$coupon->appliesToPackage($packageId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'هذا الكوبون لا ينطبق على الباقة المحددة'
            ]);
        }

        // التحقق من استخدام المستخدم الحالي للكوبون
        if (Auth::check() && $coupon->hasBeenUsedByCurrentUser('App\\Models\\Booking')) {
            return response()->json([
                'status' => 'error',
                'message' => 'لقد قمت باستخدام هذا الكوبون مسبقًا'
            ]);
        }

        $package = Package::find($packageId);
        if ($package && $coupon->min_order_amount > 0 && $package->base_price < $coupon->min_order_amount) {
            return response()->json([
                'status' => 'error',
                'message' => "الحد الأدنى للطلب لتطبيق هذا الكوبون هو {$coupon->min_order_amount} ر.س"
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم التحقق من كود الخصم بنجاح',
            'coupon' => [
                'code' => $coupon->code,
                'discount_type' => $coupon->type,
                'discount_value' => $coupon->value,
                'min_order_amount' => $coupon->min_order_amount
            ]
        ]);
    }
}
