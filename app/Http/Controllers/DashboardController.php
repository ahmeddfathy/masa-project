<?php

namespace App\Http\Controllers;

use App\Models\{Order, User, Booking, CartItem, Cart};
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        return $this->clientDashboard($user);
    }

    private function clientDashboard($user)
    {
        // إحصائيات العميل
        $cartItemsCount = CartItem::join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->where('carts.user_id', $user->id)
            ->sum('cart_items.quantity');

        $stats = [
            'orders_count' => $user->orders()->count(),
            'appointments_count' => $user->appointments()->count(), // مواعيد المتجر
            'bookings_count' => $user->bookings()->count(), // حجوزات الاستوديو
            'cart_items_count' => $cartItemsCount,
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        // الطلبات الأخيرة
        $recent_orders = $user->orders()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'uuid' => $order->uuid,
                    'order_number' => $order->order_number,
                    'created_at' => $order->created_at,
                    'status_color' => $this->getStatusColor($order->order_status),
                    'status_text' => $this->getStatusText($order->order_status)
                ];
            });

        // المواعيد القادمة (المتجر)
        $upcoming_appointments = $user->appointments()
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(4)
            ->get()
            ->map(function ($appointment) {
                $appointment->status_color = $this->getStatusColor($appointment->status);
                $appointment->status_text = $this->getStatusText($appointment->status);
                return $appointment;
            });

        // الحجوزات القادمة (الاستوديو)
        $upcoming_bookings = $user->bookings()
            ->with(['service', 'package'])
            ->where('session_date', '>=', now())
            ->orderBy('session_date')
            ->orderBy('session_time')
            ->take(4)
            ->get()
            ->map(function ($booking) {
                $booking->status_color = $this->getStatusColor($booking->status);
                $booking->status_text = $this->getStatusText($booking->status);
                return $booking;
            });

        // آخر الإشعارات
        $recent_notifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get();

        // العناوين وأرقام الهواتف
        $addresses = $user->addresses()
            ->latest()
            ->get()
            ->map(function ($address) {
                return [
                    'id' => $address->id,
                    'full_address' => $this->formatAddress($address),
                    'type' => $address->type,
                    'type_text' => $address->type_text,
                    'created_at' => $address->created_at->format('Y/m/d'),
                    'is_primary' => $address->is_primary,
                    'type_color' => $this->getAddressTypeColor($address->type)
                ];
            });

        $phones = $user->phoneNumbers()
            ->latest()
            ->get()
            ->map(function ($phone) {
                return [
                    'id' => $phone->id,
                    'phone' => $this->formatPhoneNumber($phone->phone),
                    'type' => $phone->type,
                    'type_text' => $phone->type_text,
                    'created_at' => $phone->created_at->format('Y/m/d'),
                    'is_primary' => $phone->is_primary,
                    'type_color' => $this->getPhoneTypeColor($phone->type)
                ];
            });

        // التحقق من وجود رسالة خطأ
        $error = session('error');

        return view('dashboard', compact(
            'stats',
            'recent_orders',
            'upcoming_appointments',
            'upcoming_bookings',
            'recent_notifications',
            'addresses',
            'phones',
            'error'
        ));
    }

    private function getStatusColor($status): string
    {
        return match ($status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            'processing' => 'primary',
            default => 'secondary'
        };
    }

    private function getStatusText($status): string
    {
        return match ($status) {
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'processing' => 'قيد المعالجة',
            default => 'غير معروف'
        };
    }

    private function formatPhoneNumber(string $phone): string
    {
        // تنسيق رقم الهاتف بشكل أفضل للقراءة
        return substr($phone, 0, 2) . ' ' . substr($phone, 2, 3) . ' ' . substr($phone, 5);
    }

    private function formatAddress(object $address): string
    {
        $parts = [
            $address->city,
            $address->area,
            'شارع ' . $address->street,
            $address->building_no ? 'مبنى ' . $address->building_no : null,
            $address->details
        ];

        return implode('، ', array_filter($parts));
    }

    private function getAddressTypeColor(string $type): string
    {
        return match ($type) {
            'home' => 'success',
            'work' => 'info',
            'other' => 'secondary',
            default => 'primary'
        };
    }

    private function getPhoneTypeColor(string $type): string
    {
        return match ($type) {
            'mobile' => 'success',
            'home' => 'info',
            'work' => 'warning',
            'other' => 'secondary',
            default => 'primary'
        };
    }
}
