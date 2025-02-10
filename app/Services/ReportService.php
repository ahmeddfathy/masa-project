<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Appointment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ReportService
{
  public function getSalesReport(string $period = 'month'): array
  {
    $startDate = $this->getStartDate($period);
    $endDate = now();

    $salesData = Order::where('order_status', Order::ORDER_STATUS_COMPLETED)
      ->where('payment_status', Order::PAYMENT_STATUS_PAID)
      ->whereBetween('created_at', [$startDate, $endDate])
      ->select(
        DB::raw('DATE(created_at) as date'),
        DB::raw('COUNT(*) as orders_count'),
        DB::raw('SUM(total_amount) as total_sales')
      )
      ->groupBy('date')
      ->get();

    // Fill in missing dates with zero values
    $dateRange = CarbonPeriod::create($startDate, $endDate);
    $salesByDate = collect();

    foreach ($dateRange as $date) {
      $formattedDate = $date->format('Y-m-d');
      $dayData = $salesData->firstWhere('date', $formattedDate);

      $salesByDate[$formattedDate] = [
        'sales' => $dayData ? $dayData->total_sales : 0,
        'orders' => $dayData ? $dayData->orders_count : 0
      ];
    }

    // Get sales by payment method
    $salesByPaymentMethod = Order::where('order_status', Order::ORDER_STATUS_COMPLETED)
      ->where('payment_status', Order::PAYMENT_STATUS_PAID)
      ->whereBetween('created_at', [$startDate, $endDate])
      ->select('payment_method', DB::raw('SUM(total_amount) as total'))
      ->groupBy('payment_method')
      ->get()
      ->pluck('total', 'payment_method')
      ->toArray();

    // Add peak hours analysis
    $peakHours = Order::where('order_status', Order::ORDER_STATUS_COMPLETED)
        ->where('payment_status', Order::PAYMENT_STATUS_PAID)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
        ->groupBy('hour')
        ->orderByDesc('count')
        ->limit(5)
        ->get();

    // Add customer analysis
    $customerAnalysis = Order::where('order_status', Order::ORDER_STATUS_COMPLETED)
        ->where('payment_status', Order::PAYMENT_STATUS_PAID)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->select('user_id', DB::raw('COUNT(*) as orders'), DB::raw('SUM(total_amount) as total'))
        ->groupBy('user_id')
        ->orderByDesc('total')
        ->limit(5)
        ->with('user:id,name')
        ->get();

    // Add top products analysis
    $topProducts = Product::withCount(['orderItems as total_quantity' => function ($query) use ($startDate, $endDate) {
        $query->whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->where('order_status', Order::ORDER_STATUS_COMPLETED)
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->whereBetween('created_at', [$startDate, $endDate]);
        });
    }])
    ->withSum(['orderItems as total_revenue' => function ($query) use ($startDate, $endDate) {
        $query->whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->where('order_status', Order::ORDER_STATUS_COMPLETED)
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->whereBetween('created_at', [$startDate, $endDate]);
        });
    }], 'subtotal')
    ->withSum(['orderItems as previous_revenue' => function ($query) use ($startDate) {
        $previousStart = Carbon::parse($startDate)->subDays($startDate->diffInDays(now()));
        $previousEnd = $startDate->subDay();

        $query->whereHas('order', function ($q) use ($previousStart, $previousEnd) {
            $q->where('order_status', Order::ORDER_STATUS_COMPLETED)
                ->where('payment_status', Order::PAYMENT_STATUS_PAID)
                ->whereBetween('created_at', [$previousStart, $previousEnd]);
        });
    }], 'subtotal')
    ->having('total_quantity', '>', 0)
    ->orderByDesc('total_revenue')
    ->limit(5)
    ->get()
    ->map(function ($product) {
        $previousRevenue = $product->previous_revenue ?? 0;
        $currentRevenue = $product->total_revenue ?? 0;

        $trend = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : ($currentRevenue > 0 ? 100 : 0);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'total_quantity' => $product->total_quantity,
            'total_revenue' => $product->total_revenue,
            'trend' => round($trend, 1)
        ];
    });

    return [
      'total_sales' => $salesData->sum('total_sales'),
      'orders_count' => $salesData->sum('orders_count'),
      'average_order_value' => $salesData->avg('total_sales') ?? 0,
      'daily_data' => $salesByDate,
      'payment_methods' => $salesByPaymentMethod,
      'growth' => $this->calculateGrowth($startDate, $endDate),
      'peak_hours' => $peakHours,
      'top_customers' => $customerAnalysis,
      'top_products' => $topProducts
    ];
  }

  public function getTopProducts($limit = 5, $startDate = null, $endDate = null)
  {
    if ($startDate === null) {
      $startDate = $this->getStartDate('month');
    }

    $query = Product::withCount(['orderItems as total_quantity' => function ($query) use ($startDate, $endDate) {
      $query->whereHas('order', function ($q) use ($startDate, $endDate) {
        $q->where('order_status', Order::ORDER_STATUS_COMPLETED)
          ->where('payment_status', Order::PAYMENT_STATUS_PAID)
          ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
          ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
      });
    }])
    ->withSum(['orderItems as total_revenue' => function ($query) use ($startDate, $endDate) {
      $query->whereHas('order', function ($q) use ($startDate, $endDate) {
        $q->where('order_status', Order::ORDER_STATUS_COMPLETED)
          ->where('payment_status', Order::PAYMENT_STATUS_PAID)
          ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
          ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));
      });
    }], 'subtotal');

    return $query->orderByDesc('total_quantity')
      ->limit($limit)
      ->get();
  }

  public function getOrdersReport($startDate = null, $endDate = null)
  {
    $query = Order::query();

    if ($startDate) {
      $query->whereDate('created_at', '>=', $startDate);
    }
    if ($endDate) {
      $query->whereDate('created_at', '<=', $endDate);
    }

    $totalOrders = $query->count();
    $totalRevenue = $query->sum('total_amount');

    $ordersByStatus = $query->select('order_status', DB::raw('count(*) as count'))
      ->groupBy('order_status')
      ->get()
      ->mapWithKeys(function ($item) {
        return [$item->order_status => $item->count];
      })
      ->toArray();

    // Initialize all statuses with 0 if not present
    $allStatuses = [
      Order::ORDER_STATUS_PENDING => 0,
      Order::ORDER_STATUS_PROCESSING => 0,
      Order::ORDER_STATUS_COMPLETED => 0,
      Order::ORDER_STATUS_CANCELLED => 0
    ];

    $ordersByStatus = array_merge($allStatuses, $ordersByStatus);

    $paymentsByStatus = $query->select('payment_status', DB::raw('count(*) as count'))
      ->groupBy('payment_status')
      ->get()
      ->mapWithKeys(function ($item) {
        return [$item->payment_status => $item->count];
      })
      ->toArray();

    // Initialize all payment statuses with 0 if not present
    $allPaymentStatuses = [
      Order::PAYMENT_STATUS_PENDING => 0,
      Order::PAYMENT_STATUS_PAID => 0,
      Order::PAYMENT_STATUS_FAILED => 0
    ];

    $paymentsByStatus = array_merge($allPaymentStatuses, $paymentsByStatus);

    return [
      'total_orders' => $totalOrders,
      'total_revenue' => $totalRevenue,
      'orders_by_status' => $ordersByStatus,
      'payments_by_status' => $paymentsByStatus
    ];
  }

  public function getAppointmentsReport($startDate = null, $endDate = null)
  {
    if ($startDate === null) {
      $startDate = $this->getStartDate('month');
    }

    $query = Appointment::query();

    if ($startDate) {
      $query->whereDate('appointment_date', '>=', $startDate);
    }
    if ($endDate) {
      $query->whereDate('appointment_date', '<=', $endDate);
    }

    $appointmentsData = $query->select('status', DB::raw('COUNT(*) as count'))
      ->groupBy('status')
      ->get();

    return [
      'total' => $appointmentsData->sum('count'),
      'by_status' => $appointmentsData->pluck('count', 'status')->toArray(),
      'completion_rate' => $this->calculateCompletionRate($appointmentsData),
    ];
  }

  public function getInventoryReport(): array
  {
    $products = Product::select(
      DB::raw('COUNT(*) as total_products'),
      DB::raw('COUNT(CASE WHEN stock <= 5 THEN 1 END) as low_stock_count'),
      DB::raw('COUNT(CASE WHEN stock = 0 THEN 1 END) as out_of_stock_count'),
      DB::raw('AVG(stock) as average_stock'),
      DB::raw('SUM(stock * price) as inventory_value')
    )->first();

    // Get stock distribution
    $stockRanges = [
      '0' => ['min' => 0, 'max' => 0],
      '1-5' => ['min' => 1, 'max' => 5],
      '6-20' => ['min' => 6, 'max' => 20],
      '21-50' => ['min' => 21, 'max' => 50],
      '50+' => ['min' => 51, 'max' => PHP_INT_MAX]
    ];

    $stockDistribution = [];
    foreach ($stockRanges as $label => $range) {
      $count = Product::whereBetween('stock', [$range['min'], $range['max']])->count();
      $stockDistribution[$label] = $count;
    }

    return [
      'total_products' => $products->total_products,
      'low_stock_count' => $products->low_stock_count,
      'out_of_stock_count' => $products->out_of_stock_count,
      'average_stock' => round($products->average_stock, 2),
      'inventory_value' => $products->inventory_value,
      'stock_distribution' => $stockDistribution
    ];
  }

  protected function getStartDate(string $period): Carbon
  {
    return match ($period) {
      'week' => Carbon::now()->subWeek(),
      'month' => Carbon::now()->subMonth(),
      'year' => Carbon::now()->subYear(),
      default => Carbon::now()->subMonth(),
    };
  }

  protected function calculateCompletionRate($appointmentsData): float
  {
    $completed = $appointmentsData->firstWhere('status', Appointment::STATUS_COMPLETED)?->count ?? 0;
    $total = $appointmentsData->sum('count');

    return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
  }

  protected function calculateGrowth($startDate, $endDate): array
  {
    $currentStartDate = Carbon::parse($startDate);
    $currentEndDate = Carbon::parse($endDate);

    // حساب المدة بين التاريخين
    $periodInDays = $currentStartDate->diffInDays($currentEndDate);

    // حساب الفترة السابقة
    $previousEndDate = $currentStartDate->copy()->subDay();
    $previousStartDate = $previousEndDate->copy()->subDays($periodInDays);

    $currentPeriod = Order::where('order_status', Order::ORDER_STATUS_COMPLETED)
        ->where('payment_status', Order::PAYMENT_STATUS_PAID)
        ->whereBetween('created_at', [$currentStartDate, $currentEndDate])
        ->sum('total_amount');

    $previousPeriod = Order::where('order_status', Order::ORDER_STATUS_COMPLETED)
        ->where('payment_status', Order::PAYMENT_STATUS_PAID)
        ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
        ->sum('total_amount');

    $growth = $previousPeriod > 0
        ? (($currentPeriod - $previousPeriod) / $previousPeriod) * 100
        : ($currentPeriod > 0 ? 100 : 0);

    return [
        'percentage' => round($growth, 2),
        'trend' => $growth >= 0 ? 'up' : 'down',
        'current_amount' => $currentPeriod,
        'previous_amount' => $previousPeriod
    ];
  }
}
