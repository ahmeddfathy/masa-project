<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'service', 'package']);

        // البحث برقم الحجز
        if ($request->has('booking_number') && !empty($request->booking_number)) {
            $query->where('booking_number', 'like', '%' . $request->booking_number . '%');
        }

        // تصفية حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // تصفية حسب التاريخ
        if ($request->has('date') && !empty($request->date)) {
            $query->whereDate('session_date', $request->date);
        }

        $bookings = $query->latest()->paginate(10);

        // تمرير معايير البحث للعرض للحفاظ عليها بعد تحديث الصفحة
        return view('admin.bookings.index', compact('bookings'))
            ->with('search_booking_number', $request->booking_number)
            ->with('search_status', $request->status)
            ->with('search_date', $request->date);
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

        return redirect()->back()->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    public function calendar()
    {
        // استخدم الحجوزات المستقبلية لعرضها في التقويم
        $bookings = Booking::with(['service', 'package', 'user'])
            ->whereDate('session_date', '>=', now()->subDays(30)) // عرض الحجوزات من الشهر الماضي
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
