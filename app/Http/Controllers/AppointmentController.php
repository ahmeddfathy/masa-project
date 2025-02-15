<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CartItem;
use App\Notifications\AppointmentStatusUpdated;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            DB::beginTransaction();

            $validated = $this->validateAppointment($request);

            // تحويل التاريخ والوقت إلى كائنات Carbon
            $appointmentDate = Carbon::parse($validated['appointment_date']);
            $appointmentTime = Carbon::parse($validated['appointment_time']);

            // التحقق من أن الموعد في المستقبل
            if ($appointmentDate->isPast() || ($appointmentDate->isToday() && $appointmentTime->isPast())) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حجز موعد في وقت سابق'
                ], 422);
            }

            if ($validated['service_type'] !== 'custom_design' && !$this->validateCartItem($validated['cart_item_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكنك حجز موعد لهذا المنتج'
                ], 422);
            }

            $appointment = $this->createAppointment($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حجز الموعد بنجاح',
                'redirect_url' => route('appointments.show', $appointment->reference_number)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'يرجى التحقق من البيانات المدخلة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في حجز الموعد: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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
            'appointment_time' => ['required', 'date_format:H:i'],
            'phone' => ['required', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'location' => ['required', 'string', 'in:store,client_location'],
            'address' => ['required_if:location,client_location', 'nullable', 'string', 'max:500'],
            'cart_item_id' => ['nullable', 'exists:cart_items,id']
        ]);
    }

    /**
     * التحقق من ملكية cart_item
     */
    private function validateCartItem(?int $cartItemId): bool
    {
        if (!$cartItemId) {
            return true;
        }

        try {
            $cartItem = CartItem::findOrFail($cartItemId);
            return $cartItem->cart->user_id === Auth::id();
        } catch (\Exception $e) {
            \Log::error('Error validating cart item: ' . $e->getMessage());
            return false;
        }
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
        $appointment->appointment_date = Carbon::parse($data['appointment_date'])->format('Y-m-d');
        $appointment->appointment_time = Carbon::parse($data['appointment_time'])->format('H:i:s');
        $appointment->phone = $data['phone'];
        $appointment->notes = $data['notes'] ?? null;
        $appointment->status = Appointment::STATUS_PENDING;
        $appointment->location = $data['location'];
        $appointment->address = $data['address'] ?? null;
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

    /**
     * تحديث الموعد
     */
    public function update(Request $request, Appointment $appointment)
    {
        $this->authorizeAccess($appointment);

        try {
            $validated = $request->validate([
                'appointment_date' => ['required', 'date', 'after_or_equal:today'],
                'appointment_time' => ['required'],
                'phone' => ['required', 'string', 'max:20'],
                'notes' => ['nullable', 'string', 'max:1000'],
                'location' => ['required', 'string', 'in:store,client_location'],
                'address' => ['required_if:location,client_location', 'nullable', 'string', 'max:500'],
            ]);

            $appointment->update([
                'appointment_date' => Carbon::parse($validated['appointment_date']),
                'appointment_time' => Carbon::parse($validated['appointment_time']),
                'phone' => $validated['phone'],
                'notes' => $validated['notes'],
                'location' => $validated['location'],
                'address' => $validated['address'],
            ]);

            return redirect()
                ->route('appointments.show', $appointment->reference_number)
                ->with('success', 'تم تحديث الموعد بنجاح');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث الموعد: ' . $e->getMessage());
            return back()
                ->with('error', 'حدث خطأ أثناء تحديث الموعد. الرجاء المحاولة مرة أخرى.')
                ->withInput();
        }
    }

    /**
     * إلغاء الموعد
     */
    public function cancel(Appointment $appointment)
    {
        $this->authorizeAccess($appointment);

        try {
            if ($appointment->status !== Appointment::STATUS_PENDING) {
                return back()->with('error', 'لا يمكن إلغاء هذا الموعد في الوقت الحالي');
            }

            $appointment->update([
                'status' => Appointment::STATUS_CANCELLED,
            ]);

            return redirect()
                ->route('appointments.show', $appointment->reference_number)
                ->with('success', 'تم إلغاء الموعد بنجاح');

        } catch (\Exception $e) {
            Log::error('خطأ في إلغاء الموعد: ' . $e->getMessage());
            return back()
                ->with('error', 'حدث خطأ أثناء إلغاء الموعد. الرجاء المحاولة مرة أخرى.');
        }
    }
}
