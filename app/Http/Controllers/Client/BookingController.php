<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageAddon;
use App\Models\Service;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::where('is_active', true)->get();
        $packages = Package::where('is_active', true)->get();
        $addons = PackageAddon::where('is_active', true)->get();

        // جلب صور الجاليري
        $gallery_images = Gallery::latest()->take(5)->get();

        // استرجاع البيانات المحفوظة من الجلسة إذا وجدت
        $oldFormData = session('booking_form_data', []);
        if (!empty($oldFormData)) {
            // نقل البيانات المحفوظة إلى old() helper
            foreach ($oldFormData as $key => $value) {
                session()->flash("_old_input.{$key}", $value);
            }
            // حذف البيانات من الجلسة بعد استخدامها
            session()->forget('booking_form_data');
        }

        return view('client.booking.index', compact(
            'services',
            'packages',
            'addons',
            'gallery_images'
        ));
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
            'image_consent' => 'required|in:0,1',
            'terms_consent' => 'required|accepted'
        ]);

        // احتساب السعر الإجمالي
        $package = Package::findOrFail($validated['package_id']);
        $total_amount = $package->base_price;

        // حساب سعر الإضافات
        $addons = [];
        if (!empty($validated['addons'])) {
            foreach ($validated['addons'] as $addonData) {
                if (isset($addonData['id'])) {
                    $addon = PackageAddon::findOrFail($addonData['id']);
                    $quantity = $addonData['quantity'] ?? 1;
                    $total_amount += ($addon->price * $quantity);
                    $addons[] = [
                        'id' => $addon->id,
                        'quantity' => $quantity,
                        'price' => $addon->price
                    ];
                }
            }
        }

        // حفظ بيانات الحجز في الجلسة
        $bookingData = array_merge($validated, [
            'user_id' => Auth::id(),
            'total_amount' => $total_amount,
            'addons' => $addons
        ]);
        session(['pending_booking' => $bookingData]);

        // إعداد بيانات الدفع
        $user = Auth::user();
        $paymentData = [
            "profile_id" => config('services.paytabs.profile_id'),
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => "PRE-BOOKING-" . Str::random(12),
            "cart_description" => "حجز جلسة تصوير - " . $package->name,
            "cart_currency" => config('services.paytabs.currency'),
            "cart_amount" => $total_amount,
            "callback" => route('client.bookings.payment.callback'),
            "return" => route('client.bookings.payment.return'),

            "customer_details" => [
                "name" => $user->name,
                "email" => $user->email,
                "phone" => $user->phone,
                "street1" => "عنوان العميل",
                "city" => "المدينة",
                "state" => "المحافظة",
                "country" => "EG",
                "zip" => "00000"
            ],

            "hide_shipping" => true,
            "framed" => true,
            "is_sandbox" => config('services.paytabs.is_sandbox'),
            "is_hosted" => true
        ];

        $response = Http::withHeaders([
            'Authorization' => config('services.paytabs.server_key'),
            'Content-Type' => 'application/json'
        ])->post('https://secure-egypt.paytabs.com/payment/request', $paymentData);

        if ($response->successful() && isset($response['redirect_url'])) {
            // حفظ رقم المعاملة في الجلسة
            session(['payment_transaction_id' => $response['tran_ref'] ?? null]);
            return redirect($response['redirect_url']);
        }

        return redirect()->route('client.bookings.create')
            ->with('error', 'حدث خطأ في عملية الدفع، يرجى المحاولة مرة أخرى');
    }

    public function paymentCallback(Request $request)
    {
        // طباعة كل البيانات القادمة من PayTabs
        Log::info('====== بداية بيانات الدفع ======');
        Log::info('كل البيانات:', $request->all());
        Log::info('حالة الدفع: ' . $request->input('respStatus'));
        Log::info('رسالة الدفع: ' . $request->input('respMessage'));
        Log::info('رقم المعاملة: ' . ($request->input('tran_ref') ?? $request->input('tranRef')));
        Log::info('رقم الطلب: ' . $request->input('cart_id'));
        Log::info('المبلغ: ' . $request->input('cart_amount'));
        Log::info('العملة: ' . $request->input('cart_currency'));
        Log::info('====== نهاية بيانات الدفع ======');

        // التحقق من وجود بيانات الحجز في الجلسة
        $bookingData = session('pending_booking');
        if (!$bookingData) {
            Log::error('No booking data in session');
            dd([
                'payment_data' => $request->all(),
                'session_data' => session()->all()
            ]);
            return redirect()->route('client.bookings.create')
                ->with('error', 'حدث خطأ في عملية الدفع - لم يتم العثور على بيانات الحجز');
        }

        // التحقق من حالة الدفع
        $paymentStatus = $request->input('respStatus');
        $paymentMessage = $request->input('respMessage');
        $tranRef = $request->input('tran_ref') ?? $request->input('tranRef');

        Log::info('Payment Status Details', [
            'status' => $paymentStatus,
            'message' => $paymentMessage,
            'tran_ref' => $tranRef,
            'payment_result' => $request->input('payment_result'),
            'response_code' => $request->input('resp_code'),
            'response_status' => $request->input('resp_status'),
            'card_brand' => $request->input('card_brand'),
            'card_scheme' => $request->input('card_scheme'),
            'payment_info' => $request->input('payment_info')
        ]);

        // حالات الدفع الناجح
        $successStatuses = ['A', 'H', 'P', 'V'];

        if (in_array($paymentStatus, $successStatuses)) {
            try {
                // طباعة بيانات الحجز قبل إنشائه
                Log::info('====== بيانات الحجز قبل الإنشاء ======');
                Log::info('بيانات الحجز:', $bookingData);
                Log::info('====== نهاية بيانات الحجز ======');

                // التحقق من عدم وجود حجز سابق بنفس رقم المعاملة
                if ($tranRef) {
                    $existingBooking = Booking::where('payment_transaction_id', $tranRef)->first();
                    if ($existingBooking) {
                        Log::info('Booking already exists', [
                            'booking_id' => $existingBooking->id,
                            'payment_data' => $request->all()
                        ]);
                        return redirect()->route('client.bookings.success', $existingBooking)
                            ->with('success', 'تم الدفع وتأكيد الحجز بنجاح!');
                    }
                }

                // تحويل terms_consent إلى boolean
                $terms_consent = $bookingData['terms_consent'] ?? 1;

                // إنشاء الحجز بعد نجاح الدفع
                $booking = Booking::create([
                    'user_id' => $bookingData['user_id'],
                    'service_id' => $bookingData['service_id'],
                    'package_id' => $bookingData['package_id'],
                    'booking_date' => now(),
                    'session_date' => $bookingData['session_date'],
                    'session_time' => $bookingData['session_time'],
                    'baby_name' => $bookingData['baby_name'],
                    'baby_birth_date' => $bookingData['baby_birth_date'],
                    'gender' => $bookingData['gender'],
                    'notes' => $bookingData['notes'],
                    'status' => 'confirmed',
                    'total_amount' => $bookingData['total_amount'],
                    'image_consent' => $bookingData['image_consent'],
                    'terms_consent' => $terms_consent,
                    'payment_transaction_id' => $tranRef
                ]);

                // إضافة الإضافات
                if (!empty($bookingData['addons'])) {
                    foreach ($bookingData['addons'] as $addon) {
                        $booking->addons()->attach($addon['id'], [
                            'quantity' => $addon['quantity'],
                            'price_at_booking' => $addon['price']
                        ]);
                    }
                }

                // طباعة بيانات الحجز بعد إنشائه
                Log::info('====== بيانات الحجز بعد الإنشاء ======');
                Log::info('تم إنشاء الحجز بنجاح:', [
                    'booking_id' => $booking->id,
                    'payment_status' => $paymentStatus,
                    'payment_message' => $paymentMessage,
                    'booking_details' => $booking->toArray(),
                    'payment_details' => $request->all()
                ]);
                Log::info('====== نهاية بيانات الحجز ======');

                // مسح بيانات الجلسة
                session()->forget(['pending_booking', 'payment_transaction_id']);

                return redirect()->route('client.bookings.success', $booking)
                    ->with('success', 'تم الدفع وتأكيد الحجز بنجاح!');
            } catch (\Exception $e) {
                Log::error('Error creating booking after payment', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'booking_data' => $bookingData,
                    'payment_data' => $request->all()
                ]);

                // طباعة الخطأ بالتفصيل
                dd([
                    'error' => $e->getMessage(),
                    'booking_data' => $bookingData,
                    'payment_data' => $request->all()
                ]);

                return redirect()->route('client.bookings.create')
                    ->with('error', 'تم الدفع بنجاح ولكن حدث خطأ في تسجيل الحجز. سيتم التواصل معك قريباً.');
            }
        }

        // في حالة فشل الدفع
        Log::warning('Payment failed', [
            'status' => $paymentStatus,
            'message' => $paymentMessage,
            'tran_ref' => $tranRef,
            'full_response' => $request->all()
        ]);

        // طباعة بيانات الفشل
        dd([
            'payment_status' => $paymentStatus,
            'payment_message' => $paymentMessage,
            'transaction_ref' => $tranRef,
            'all_payment_data' => $request->all(),
            'session_data' => session()->all()
        ]);

        session()->forget(['pending_booking', 'payment_transaction_id']);
        return redirect()->route('client.bookings.create')
            ->with('error', 'فشلت عملية الدفع: ' . ($paymentMessage ?: 'حدث خطأ غير متوقع'));
    }

    public function paymentReturn(Request $request)
    {
        // نفس منطق الـ callback
        return $this->paymentCallback($request);
    }

    public function success(Booking $booking)
    {
        return view('client.booking.success', compact('booking'));
    }

    public function myBookings()
    {
        $bookings = Auth::user()->bookings()
            ->with(['service', 'package', 'addons'])
            ->latest()
            ->paginate(10);
        return view('client.booking.my-bookings', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // التحقق من أن الحجز يخص المستخدم الحالي
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['service', 'package', 'addons']);
        return view('client.booking.show', compact('booking'));
    }

    public function saveFormData(Request $request)
    {
        // حفظ بيانات النموذج في الجلسة مع تضمين حقول الموافقة
        $formData = $request->all();
        $formData['image_consent'] = $request->input('image_consent', '0');
        $formData['terms_consent'] = $request->has('terms_consent');

        session(['booking_form_data' => $formData]);

        // التوجيه حسب نوع الطلب
        return redirect()->route($request->query('redirect', 'register'));
    }
}
