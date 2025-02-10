<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
  public function index(Request $request)
  {
    try {
        $query = Order::with(['user', 'items.product'])
            ->latest();

        // Filter by order number
        if ($request->order_number) {
            $query->where('order_number', 'like', "%{$request->order_number}%");
        }

        // Filter by status
        if ($request->order_status) {
            $query->where('order_status', $request->order_status);
        }

        // Filter by payment status
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by customer name, email or order number
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', function ($userQuery) use ($request) {
                      $userQuery->where('name', 'like', "%{$request->search}%")
                               ->orWhere('email', 'like', "%{$request->search}%");
                  });
            });
        }

        // Get statistics
        $stats = [
            'total_orders' => Order::count(),
            'completed_orders' => Order::where('order_status', Order::ORDER_STATUS_COMPLETED)->count(),
            'processing_orders' => Order::where('order_status', Order::ORDER_STATUS_PROCESSING)->count(),
            'total_revenue' => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)->sum('total_amount')
        ];

        $orders = $query->paginate(10);

        // Transform orders data
        $orders->through(function ($order) {
            return [
                'id' => $order->id,
                'uuid' => $order->uuid,
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name,
                'customer_phone' => $order->user->phone ?? '-',
                'items_count' => $order->items->count(),
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price,
                        'total' => $item->quantity * $item->product->price
                    ];
                }),
                'total' => $order->total_amount,
                'status' => $order->order_status,
                'status_text' => match($order->order_status) {
                    Order::ORDER_STATUS_COMPLETED => 'مكتمل',
                    Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
                    Order::ORDER_STATUS_PENDING => 'معلق',
                    Order::ORDER_STATUS_CANCELLED => 'ملغي',
                    default => 'غير معروف'
                },
                'status_color' => match($order->order_status) {
                    Order::ORDER_STATUS_COMPLETED => 'success',
                    Order::ORDER_STATUS_PROCESSING => 'info',
                    Order::ORDER_STATUS_PENDING => 'warning',
                    Order::ORDER_STATUS_CANCELLED => 'danger',
                    default => 'secondary'
                },
                'payment_status' => $order->payment_status,
                'payment_status_text' => match($order->payment_status) {
                    Order::PAYMENT_STATUS_PAID => 'مدفوع',
                    Order::PAYMENT_STATUS_PENDING => 'معلق',
                    Order::PAYMENT_STATUS_FAILED => 'فشل',
                    default => 'غير معروف'
                },
                'payment_status_color' => match($order->payment_status) {
                    Order::PAYMENT_STATUS_PAID => 'success',
                    Order::PAYMENT_STATUS_PENDING => 'warning',
                    Order::PAYMENT_STATUS_FAILED => 'danger',
                    default => 'secondary'
                },
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'created_at_formatted' => $order->created_at->format('Y/m/d')
            ];
        });

        // Get available statuses for filtering
        $orderStatuses = [
            Order::ORDER_STATUS_PENDING => 'معلق',
            Order::ORDER_STATUS_PROCESSING => 'قيد المعالجة',
            Order::ORDER_STATUS_COMPLETED => 'مكتمل',
            Order::ORDER_STATUS_CANCELLED => 'ملغي'
        ];

        $paymentStatuses = [
            Order::PAYMENT_STATUS_PENDING => 'معلق',
            Order::PAYMENT_STATUS_PAID => 'مدفوع',
            Order::PAYMENT_STATUS_FAILED => 'فشل'
        ];

        return view('admin.orders.index', compact('orders', 'orderStatuses', 'paymentStatuses', 'stats'));
    } catch (\Exception $e) {
        Log::error('Error in orders index: ' . $e->getMessage());
        return back()->with('error', 'حدث خطأ أثناء تحميل الطلبات');
    }
  }

  public function show(Order $order)
  {
    // تحميل العلاقات المطلوبة
    $order->load([
        'user.addresses',
        'user.phoneNumbers',
        'items.product',
        'items.appointment'
    ]);

    // فصل المنتجات حسب المواعيد
    $itemsWithAppointments = $order->items->filter(function($item) {
        return $item->appointment !== null;
    });

    $itemsWithoutAppointments = $order->items->filter(function($item) {
        return $item->appointment === null;
    });

    // الحصول على العناوين الإضافية
    $additionalAddresses = $order->user->addresses()
        ->where('id', '!=', $order->address_id)
        ->get();

    // الحصول على أرقام الهواتف الإضافية
    $additionalPhones = $order->user->phoneNumbers()
        ->where('phone', '!=', $order->phone)
        ->get();

    return view('admin.orders.show', compact(
        'order',
        'itemsWithAppointments',
        'itemsWithoutAppointments',
        'additionalAddresses',
        'additionalPhones'
    ));
  }

  public function updateStatus(Request $request, Order $order)
  {
    \Log::info('Updating order status - Start', [
        'order_id' => $order->id,
        'current_status' => $order->order_status,
        'new_status' => $request->order_status,
        'request_data' => $request->all(),
        'order_data' => $order->toArray()
    ]);

    try {
        $validated = $request->validate([
            'order_status' => ['required', 'string', 'in:' . implode(',', [
                Order::ORDER_STATUS_PENDING,
                Order::ORDER_STATUS_PROCESSING,
                Order::ORDER_STATUS_COMPLETED,
                Order::ORDER_STATUS_CANCELLED,
            ])],
        ]);

        DB::beginTransaction();

        $updated = $order->update([
            'order_status' => $validated['order_status']
        ]);

        \Log::info('Update operation result', [
            'updated' => $updated,
            'new_order_data' => $order->fresh()->toArray()
        ]);

        DB::commit();

        // Notify the customer about the status change
        if ($order->user) {
            $order->user->notify(new OrderStatusUpdated($order));
        }

        return back()->with('success', 'Order status updated successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating order status', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return back()->with('error', 'Failed to update order status: ' . $e->getMessage());
    }
  }

  public function updatePaymentStatus(Request $request, Order $order)
  {
    $validated = $request->validate([
      'payment_status' => ['required', 'string', 'in:' . implode(',', [
        Order::PAYMENT_STATUS_PENDING,
        Order::PAYMENT_STATUS_PAID,
        Order::PAYMENT_STATUS_FAILED,
      ])],
    ]);

    $order->update([
      'payment_status' => $validated['payment_status']
    ]);

    return back()->with('success', 'Payment status updated successfully.');
  }
}
