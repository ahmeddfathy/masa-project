<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Gallery;
use App\Models\Service;
use App\Models\Package;
use App\Models\Booking;
use App\Models\PackageAddon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Default values for stats
            $stats = [
                'orders' => 0,
                'users' => 0,
                'products' => 0,
                'revenue' => 0,
                'pending_orders' => 0,
                'processing_orders' => 0,
                'completed_orders' => 0,
                'out_for_delivery_orders' => 0,
                'on_the_way_orders' => 0,
                'delivered_orders' => 0,
                'returned_orders' => 0,
                'today_orders' => 0,
                'today_revenue' => 0,
                'month_orders' => 0,
                'month_revenue' => 0,
                // إضافة إحصائيات الاستوديو
                'total_gallery_images' => 0,
                'total_services' => 0,
                'total_packages' => 0,
                'total_bookings' => 0,
                'pending_bookings' => 0,
                'completed_bookings' => 0,
                'today_bookings' => 0,
                'month_bookings' => 0,
                'total_addons' => 0,
                'studio_revenue' => 0,
                'today_studio_revenue' => 0,
                'month_studio_revenue' => 0
            ];

            // الإحصائيات الأساسية
            $stats = array_merge($stats, [
                'orders' => Order::count(),
                'users' => User::count(),
                'products' => Product::count(),
                'revenue' => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)
                    ->sum('total_amount'),
                'pending_orders' => Order::where('order_status', Order::ORDER_STATUS_PENDING)->count(),
                'processing_orders' => Order::where('order_status', Order::ORDER_STATUS_PROCESSING)->count(),
                'completed_orders' => Order::where('order_status', Order::ORDER_STATUS_COMPLETED)->count(),
                'out_for_delivery_orders' => Order::where('order_status', Order::ORDER_STATUS_OUT_FOR_DELIVERY)->count(),
                'on_the_way_orders' => Order::where('order_status', Order::ORDER_STATUS_ON_THE_WAY)->count(),
                'delivered_orders' => Order::where('order_status', Order::ORDER_STATUS_DELIVERED)->count(),
                'returned_orders' => Order::where('order_status', Order::ORDER_STATUS_RETURNED)->count(),
                'today_orders' => Order::whereDate('created_at', Carbon::today())->count(),
                'today_revenue' => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)
                    ->whereDate('created_at', Carbon::today())
                    ->sum('total_amount'),
                'month_orders' => Order::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->count(),
                'month_revenue' => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('total_amount'),

                // إحصائيات الاستوديو
                'total_gallery_images' => Gallery::count(),
                'total_services' => Service::where('is_active', true)->count(),
                'total_packages' => Package::where('is_active', true)->count(),
                'total_bookings' => Booking::count(),
                'pending_bookings' => Booking::where('status', 'pending')->count(),
                'completed_bookings' => Booking::where('status', 'completed')->count(),
                'today_bookings' => Booking::whereDate('booking_date', Carbon::today())->count(),
                'month_bookings' => Booking::whereMonth('booking_date', Carbon::now()->month)
                    ->whereYear('booking_date', Carbon::now()->year)
                    ->count(),
                'total_addons' => PackageAddon::where('is_active', true)->count(),
                'studio_revenue' => Booking::where('status', 'completed')->sum('total_amount'),
                'today_studio_revenue' => Booking::where('status', 'completed')
                    ->whereDate('booking_date', Carbon::today())
                    ->sum('total_amount'),
                'month_studio_revenue' => Booking::where('status', 'completed')
                    ->whereMonth('booking_date', Carbon::now()->month)
                    ->whereYear('booking_date', Carbon::now()->year)
                    ->sum('total_amount')
            ]);

            // تحسين بيانات المبيعات للرسم البياني
            $salesData = Order::where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->where('created_at', '>=', now()->subMonths(12))
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(total_amount) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // تجهيز بيانات الرسم البياني بشكل أفضل
            $chartData = [];
            $chartLabels = [];
            $monthlyGrowth = [];
            $previousTotal = 0;

            foreach ($salesData as $data) {
                $total = $data->total;
                $chartLabels[] = Carbon::createFromFormat('Y-m', $data->month)->translatedFormat('F Y');
                $chartData[] = $total;

                // حساب نسبة النمو
                $growth = $previousTotal > 0 ? round((($total - $previousTotal) / $previousTotal) * 100, 1) : 0;
                $monthlyGrowth[] = $growth;
                $previousTotal = $total;
            }

            // إضافة الشهر الحالي إذا لم يكن موجوداً
            if (empty($chartLabels) || end($chartLabels) !== now()->translatedFormat('F Y')) {
                $chartLabels[] = now()->translatedFormat('F Y');
                $chartData[] = 0;
                $monthlyGrowth[] = 0;
            }

            // إضافة بيانات إيرادات الاستوديو للرسم البياني
            $studioSalesData = Booking::where('status', 'completed')
                ->where('booking_date', '>=', now()->subMonths(12))
                ->select(
                    DB::raw('DATE_FORMAT(booking_date, "%Y-%m") as month'),
                    DB::raw('SUM(total_amount) as total'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $studioChartData = [];
            $studioMonthlyGrowth = [];
            $studioPreviousTotal = 0;

            foreach ($studioSalesData as $data) {
                $total = $data->total;
                $studioChartData[] = $total;

                // حساب نسبة النمو للاستوديو
                $growth = $studioPreviousTotal > 0 ? round((($total - $studioPreviousTotal) / $studioPreviousTotal) * 100, 1) : 0;
                $studioMonthlyGrowth[] = $growth;
                $studioPreviousTotal = $total;
            }

            // إضافة الشهر الحالي إذا لم يكن موجوداً
            if (count($studioChartData) < count($chartLabels)) {
                $studioChartData[] = 0;
                $studioMonthlyGrowth[] = 0;
            }

            // تحسين إحصائيات حالات الطلبات
            $orderStats = [
                Order::ORDER_STATUS_COMPLETED => Order::where('order_status', Order::ORDER_STATUS_COMPLETED)->count(),
                Order::ORDER_STATUS_PROCESSING => Order::where('order_status', Order::ORDER_STATUS_PROCESSING)->count(),
                Order::ORDER_STATUS_PENDING => Order::where('order_status', Order::ORDER_STATUS_PENDING)->count(),
                Order::ORDER_STATUS_CANCELLED => Order::where('order_status', Order::ORDER_STATUS_CANCELLED)->count(),
                Order::ORDER_STATUS_OUT_FOR_DELIVERY => Order::where('order_status', Order::ORDER_STATUS_OUT_FOR_DELIVERY)->count(),
                Order::ORDER_STATUS_ON_THE_WAY => Order::where('order_status', Order::ORDER_STATUS_ON_THE_WAY)->count(),
                Order::ORDER_STATUS_DELIVERED => Order::where('order_status', Order::ORDER_STATUS_DELIVERED)->count(),
                Order::ORDER_STATUS_RETURNED => Order::where('order_status', Order::ORDER_STATUS_RETURNED)->count()
            ];

            // إضافة إحصائيات حالات الحجوزات
            $bookingStats = [
                'completed' => Booking::where('status', 'completed')->count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'cancelled' => Booking::where('status', 'cancelled')->count()
            ];

            // تحسين عرض أحدث الطلبات
            $recentOrders = Order::with(['user', 'items.product'])
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'uuid' => $order->uuid,
                        'order_number' => $order->order_number,
                        'user_name' => $order->user->name,
                        'total' => $order->total_amount,
                        'payment_status' => $order->payment_status,
                        'order_status' => $order->order_status,
                        'created_at' => $order->created_at->format('Y-m-d H:i'),
                        'items_count' => $order->items->count(),
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product->name,
                                'quantity' => $item->quantity,
                                'unit_price' => $item->product->price,
                                'total_price' => $item->quantity * $item->product->price
                            ];
                        }),
                        'status_color' => match($order->order_status) {
                            Order::ORDER_STATUS_COMPLETED => 'success',
                            Order::ORDER_STATUS_PROCESSING => 'info',
                            Order::ORDER_STATUS_PENDING => 'warning',
                            Order::ORDER_STATUS_CANCELLED => 'danger',
                            Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'primary',
                            Order::ORDER_STATUS_ON_THE_WAY => 'info',
                            Order::ORDER_STATUS_DELIVERED => 'success',
                            Order::ORDER_STATUS_RETURNED => 'secondary',
                            default => 'secondary'
                        },
                        'status_text' => match($order->order_status) {
                            Order::ORDER_STATUS_COMPLETED => 'مكتمل',
                            Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
                            Order::ORDER_STATUS_PENDING => 'معلق',
                            Order::ORDER_STATUS_CANCELLED => 'ملغي',
                            Order::ORDER_STATUS_OUT_FOR_DELIVERY => 'قيد التوصيل',
                            Order::ORDER_STATUS_ON_THE_WAY => 'في الطريق',
                            Order::ORDER_STATUS_DELIVERED => 'تم التوصيل',
                            Order::ORDER_STATUS_RETURNED => 'مرتجع',
                            default => 'غير معروف'
                        },
                        'payment_status_color' => match($order->payment_status) {
                            Order::PAYMENT_STATUS_PAID => 'success',
                            Order::PAYMENT_STATUS_PENDING => 'warning',
                            Order::PAYMENT_STATUS_FAILED => 'danger',
                            default => 'secondary'
                        },
                        'payment_status_text' => match($order->payment_status) {
                            Order::PAYMENT_STATUS_PAID => 'مدفوع',
                            Order::PAYMENT_STATUS_PENDING => 'معلق',
                            Order::PAYMENT_STATUS_FAILED => 'فشل',
                            default => 'غير معروف'
                        }
                    ];
                });

            // المنتجات الأكثر مبيعاً
            $topProducts = Product::withCount(['orderItems as sales_count' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('payment_status', Order::PAYMENT_STATUS_PAID);
                });
            }])
                ->orderByDesc('sales_count')
                ->take(5)
                ->get();

            return view('admin.dashboard', compact(
                'stats',
                'chartLabels',
                'chartData',
                'monthlyGrowth',
                'studioChartData',
                'studioMonthlyGrowth',
                'recentOrders',
                'orderStats',
                'bookingStats',
                'topProducts'
            ));
        } catch (\Exception $e) {
            Log::error('Dashboard data loading error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Default values
            $defaultStatuses = [
                Order::ORDER_STATUS_PENDING => 0,
                Order::ORDER_STATUS_PROCESSING => 0,
                Order::ORDER_STATUS_COMPLETED => 0,
                Order::ORDER_STATUS_CANCELLED => 0
            ];

            return view('admin.dashboard', [
                'stats' => [
                    'orders' => 0,
                    'users' => 0,
                    'products' => 0,
                    'revenue' => 0,
                    'today_orders' => 0,
                    'month_orders' => 0,
                    'today_revenue' => 0,
                    'month_revenue' => 0,
                    'pending_orders' => 0,
                    'processing_orders' => 0,
                    'completed_orders' => 0,
                    // إضافة القيم الافتراضية لإحصائيات الاستوديو
                    'total_gallery_images' => 0,
                    'total_services' => 0,
                    'total_packages' => 0,
                    'total_bookings' => 0,
                    'pending_bookings' => 0,
                    'completed_bookings' => 0,
                    'today_bookings' => 0,
                    'month_bookings' => 0,
                    'total_addons' => 0,
                    'studio_revenue' => 0,
                    'today_studio_revenue' => 0,
                    'month_studio_revenue' => 0
                ],
                'chartLabels' => [now()->format('M Y')],
                'chartData' => [0],
                'monthlyGrowth' => [0],
                'studioChartData' => [0],
                'studioMonthlyGrowth' => [0],
                'recentOrders' => collect([]),
                'orderStats' => $defaultStatuses,
                'bookingStats' => [
                    'completed' => 0,
                    'pending' => 0,
                    'cancelled' => 0
                ],
                'error' => 'Error loading dashboard data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * تحديث FCM token للمستخدم
     */
    public function updateFcmToken(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validator = Validator::make($request->all(), [
                'fcm_token' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صالحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // تحديث FCM token للمستخدم الحالي
            $user = auth()->user();

            // تحديث الـ token في جدول المستخدمين
            $user->update([
                'fcm_token' => $request->fcm_token
            ]);

            // تسجيل نجاح العملية
            Log::info('FCM Token updated successfully for user: ' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث FCM token بنجاح'
            ]);

        } catch (\Exception $e) {
            // تسجيل الخطأ
            Log::error('Error updating FCM token: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث FCM token'
            ], 500);
        }
    }
}
