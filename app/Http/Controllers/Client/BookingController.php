<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;

use App\Services\Booking\BookingService;
use App\Services\Booking\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    protected $bookingService;
    protected $availabilityService;

    public function __construct(BookingService $bookingService, AvailabilityService $availabilityService)
    {
        $this->bookingService = $bookingService;
        $this->availabilityService = $availabilityService;
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

            // إضافة UUID ورقم الحجز العشوائي
            $validated['uuid'] = (string) Str::uuid();

            $booking = $this->bookingService->createBooking($validated, $totalAmount, Auth::id());

            return redirect()->route('client.bookings.success', $booking->uuid)
                ->with('success', 'تم إنشاء الحجز بنجاح!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء إنشاء الحجز. الرجاء المحاولة مرة أخرى.');
        }
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
}
