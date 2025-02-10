<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CartItem;
use App\Notifications\AppointmentStatusUpdated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    /**
     * عرض قائمة المواعيد للمستخدم
     */
    public function index()
    {
        $appointments = Auth::user()
            ->appointments()
            ->latest()
            ->paginate(10);

        return view('appointments.index', compact('appointments'));
    }

    /**
     * عرض نموذج إنشاء موعد جديد
     */
    public function create()
    {
        return view('appointments.create');
    }

    /**
     * حفظ موعد جديد
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول أولاً لحجز موعد'
            ], 401);
        }

        try {
            $validated = $this->validateAppointment($request);

            if ($validated['service_type'] !== 'custom_design' && !$this->validateCartItem($validated['cart_item_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك حجز موعد لهذا المنتج'
                ], 403);
            }

            $appointment = $this->createAppointment($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم حجز الموعد بنجاح',
                'appointment' => [
                    'id' => $appointment->id,
                    'date' => $appointment->appointment_date->format('Y-m-d'),
                    'time' => $appointment->appointment_time->format('H:i'),
                    'status' => $appointment->status,
                    'url' => route('appointments.show', $appointment)
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('خطأ في حجز الموعد: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حجز الموعد. الرجاء المحاولة مرة أخرى.'
            ], 500);
        }
    }

    /**
     * عرض تفاصيل الموعد
     */
    public function show(Appointment $appointment)
    {
        $this->authorizeAccess($appointment);
        return view('appointments.show', compact('appointment'));
    }

    /**
     * عرض قائمة المواعيد للمشرف
     */
    public function adminIndex()
    {
        $this->authorize('viewAny', Appointment::class);

        $appointments = Appointment::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * التحقق من صحة البيانات
     */
    private function validateAppointment(Request $request): array
    {
        return $request->validate([
            'service_type' => ['required', 'string', 'in:new_abaya,alteration,repair,custom_design'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required'],
            'phone' => ['required', 'string', 'max:20'],
            'notes' => ['required_if:service_type,custom_design', 'nullable', 'string', 'max:1000'],
            'location' => ['required', 'string', 'in:store,client_location'],
            'address' => ['required_if:location,client_location', 'nullable', 'string', 'max:500'],
            'cart_item_id' => ['nullable', 'exists:cart_items,id']
        ]);
    }

    /**
     * التحقق من ملكية cart_item
     */
    private function validateCartItem(int $cartItemId): bool
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        return $cartItem->cart->user_id === Auth::id();
    }

    /**
     * إنشاء موعد جديد
     */
    private function createAppointment(array $data): Appointment
    {
        $appointment = new Appointment();
        $appointment->user_id = Auth::id();
        $appointment->cart_item_id = $data['cart_item_id'] ?? null;
        $appointment->service_type = $data['service_type'];
        $appointment->appointment_date = Carbon::parse($data['appointment_date']);
        $appointment->appointment_time = Carbon::parse($data['appointment_time']);
        $appointment->phone = $data['phone'];
        $appointment->notes = $data['service_type'] === 'custom_design'
            ? 'تصميم مخصص: ' . $data['notes']
            : $data['notes'];
        $appointment->status = Appointment::STATUS_PENDING;
        $appointment->location = $data['location'];
        $appointment->address = $data['address'];
        $appointment->save();

        return $appointment;
    }

    /**
     * التحقق من صلاحية الوصول
     */
    private function authorizeAccess(Appointment $appointment): void
    {
        if ($appointment->user_id !== Auth::id()) {
            abort(403);
        }
    }


}
