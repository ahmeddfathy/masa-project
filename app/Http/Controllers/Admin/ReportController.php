<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
  protected $reportService;

  public function __construct(ReportService $reportService)
  {
    $this->reportService = $reportService;
  }

  public function index(Request $request)
  {
    $period = $request->get('period', 'month');
    $startDate = null;
    $endDate = null;
    $paymentMethod = $request->get('payment_method');

    if ($period === 'custom') {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
    }

    // Get all report data with payment method filter
    $salesReport = $this->reportService->getSalesReport($period, $startDate, $endDate, $paymentMethod);
    $topProducts = $this->reportService->getTopProducts($period, $startDate, $endDate);
    $appointmentsReport = $this->reportService->getAppointmentsReport($startDate, $endDate);
    $inventoryReport = $this->reportService->getInventoryReport();

    // Ensure payment methods data exists
    if (!isset($salesReport['payment_methods'])) {
        $salesReport['payment_methods'] = [
            'card' => 0,
            'cash' => 0,
            'other' => 0
        ];
    }

    // Ensure peak hours data exists
    if (!isset($salesReport['peak_hours'])) {
        $salesReport['peak_hours'] = array_map(function($hour) {
            return ['hour' => $hour, 'count' => 0];
        }, range(0, 23));
    }

    return view('admin.reports.index', compact(
        'salesReport',
        'topProducts',
        'appointmentsReport',
        'inventoryReport',
        'period'
    ));
  }
}
