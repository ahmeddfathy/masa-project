<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Package;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'service', 'package'])
            ->latest()
            ->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'service', 'package', 'addons']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        $booking->update([
            'status' => $validated['status']
        ]);

        // يمكن إضافة إشعار للعميل هنا

        return redirect()->back()->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    public function calendar()
    {
        // يمكنك إضافة المنطق الخاص بعرض التقويم هنا
        $bookings = Booking::with(['service', 'package', 'user'])
            ->whereDate('session_date', '>=', now())
            ->get();

        return view('admin.bookings.calendar', compact('bookings'));
    }

    public function reports()
    {
        // إحصائيات أساسية
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'total_revenue' => Booking::where('status', 'completed')->sum('total_amount'),
            'monthly_bookings' => Booking::whereMonth('created_at', now()->month)->count(),
            'monthly_revenue' => Booking::whereMonth('created_at', now()->month)
                ->where('status', 'completed')
                ->sum('total_amount')
        ];

        // بيانات الحجوزات الشهرية للرسم البياني
        $monthlyBookings = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // بيانات حالات الحجوزات للرسم البياني الدائري
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        return view('admin.bookings.reports', compact(
            'stats',
            'monthlyBookings',
            'pendingBookings',
            'confirmedBookings',
            'completedBookings',
            'cancelledBookings'
        ));
    }
}
