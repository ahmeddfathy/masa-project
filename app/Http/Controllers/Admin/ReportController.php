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

    if ($period === 'custom') {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
    }

    $salesReport = $this->reportService->getSalesReport($period, $startDate, $endDate);
    $topProducts = $this->reportService->getTopProducts($period, $startDate, $endDate);
    $appointmentsReport = $this->reportService->getAppointmentsReport($startDate, $endDate);
    $inventoryReport = $this->reportService->getInventoryReport();

    return view('admin.reports.index', compact(
      'salesReport',
      'topProducts',
      'appointmentsReport',
      'inventoryReport',
      'period'
    ));
  }
}
