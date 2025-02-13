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
            'addons' => 'nullable|array'
        ]);

        // احتساب السعر الإجمالي
        $package = Package::findOrFail($validated['package_id']);
        $total_amount = $package->base_price;

        // إنشاء الحجز
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_id' => $validated['service_id'],
            'package_id' => $validated['package_id'],
            'booking_date' => now(),
            'session_date' => $validated['session_date'],
            'session_time' => $validated['session_time'],
            'baby_name' => $validated['baby_name'],
            'baby_birth_date' => $validated['baby_birth_date'],
            'gender' => $validated['gender'],
            'notes' => $validated['notes'],
            'status' => 'pending',
            'total_amount' => $total_amount
        ]);

        // إضافة الإضافات إذا وجدت
        if (!empty($validated['addons'])) {
            foreach ($validated['addons'] as $addonData) {
                if (isset($addonData['id'])) {
                    $addon = PackageAddon::findOrFail($addonData['id']);
                    $quantity = $addonData['quantity'] ?? 1;

                    $booking->addons()->attach($addon->id, [
                        'quantity' => $quantity,
                        'price_at_booking' => $addon->price
                    ]);

                    $total_amount += ($addon->price * $quantity);
                }
            }

            // تحديث السعر الإجمالي بعد إضافة الإضافات
            $booking->update(['total_amount' => $total_amount]);
        }

        return redirect()->route('client.bookings.success', $booking)
            ->with('success', 'تم إنشاء الحجز بنجاح!');
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
        // حفظ بيانات النموذج في الجلسة
        session(['booking_form_data' => $request->all()]);

        // التوجيه حسب نوع الطلب
        return redirect()->route($request->query('redirect', 'register'));
    }
}
