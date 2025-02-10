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

    $salesReport = $this->reportService->getSalesReport($period);
    $topProducts = $this->reportService->getTopProducts($period);
    $appointmentsReport = $this->reportService->getAppointmentsReport($period);
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
