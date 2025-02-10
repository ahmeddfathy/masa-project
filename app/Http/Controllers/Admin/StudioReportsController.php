<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudioReportsController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));

        // Get date ranges based on period
        $dates = $this->getDateRanges($period, $date);

        // Get studio reports data
        $studioReport = $this->getStudioReport($dates);
        $appointmentsReport = $this->getAppointmentsReport($dates);
        $topServices = $this->getTopServices($dates);
        $topPackages = $this->getTopPackages($dates);

        return view('admin.studio-reports.index', compact(
            'studioReport',
            'appointmentsReport',
            'topServices',
            'topPackages',
            'period'
        ));
    }

    private function getDateRanges($period, $date)
    {
        $end = Carbon::parse($date);

        switch ($period) {
            case 'week':
                $start = $end->copy()->startOfWeek();
                $previousStart = $start->copy()->subWeek();
                $previousEnd = $end->copy()->subWeek();
                break;
            case 'month':
                $start = $end->copy()->startOfMonth();
                $previousStart = $start->copy()->subMonth();
                $previousEnd = $end->copy()->subMonth();
                break;
            case 'quarter':
                $start = $end->copy()->startOfQuarter();
                $previousStart = $start->copy()->subQuarter();
                $previousEnd = $end->copy()->subQuarter();
                break;
            case 'year':
                $start = $end->copy()->startOfYear();
                $previousStart = $start->copy()->subYear();
                $previousEnd = $end->copy()->subYear();
                break;
            default:
                $start = $end->copy()->startOfMonth();
                $previousStart = $start->copy()->subMonth();
                $previousEnd = $end->copy()->subMonth();
        }

        return [
            'start' => $start,
            'end' => $end,
            'previousStart' => $previousStart,
            'previousEnd' => $previousEnd
        ];
    }

    private function getStudioReport($dates)
    {
        // Current period revenue
        $currentRevenue = Booking::where('status', 'completed')
            ->whereBetween('booking_date', [$dates['start'], $dates['end']])
            ->sum('total_amount');

        // Previous period revenue
        $previousRevenue = Booking::where('status', 'completed')
            ->whereBetween('booking_date', [$dates['previousStart'], $dates['previousEnd']])
            ->sum('total_amount');

        // Calculate growth
        $growth = $previousRevenue > 0
            ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100
            : 100;

        // Get daily revenue for chart
        $dailyRevenue = Booking::where('status', 'completed')
            ->whereBetween('booking_date', [$dates['start'], $dates['end']])
            ->select(
                DB::raw('DATE(booking_date) as date'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('COUNT(*) as bookings_count')
            )
            ->groupBy('date')
            ->get();

        return [
            'total_revenue' => $currentRevenue,
            'growth' => [
                'percentage' => round($growth, 1),
                'trend' => $growth >= 0 ? 'up' : 'down',
                'current_amount' => $currentRevenue,
                'previous_amount' => $previousRevenue
            ],
            'daily_revenue' => $dailyRevenue,
            'bookings_count' => Booking::whereBetween('booking_date', [$dates['start'], $dates['end']])->count(),
            'average_booking_value' => $currentRevenue > 0
                ? $currentRevenue / Booking::whereBetween('booking_date', [$dates['start'], $dates['end']])->count()
                : 0
        ];
    }

    private function getAppointmentsReport($dates)
    {
        $totalBookings = Booking::whereBetween('booking_date', [$dates['start'], $dates['end']])->count();
        $completedBookings = Booking::where('status', 'completed')
            ->whereBetween('booking_date', [$dates['start'], $dates['end']])
            ->count();

        return [
            'completion_rate' => $totalBookings > 0
                ? round(($completedBookings / $totalBookings) * 100, 1)
                : 0,
            'status_distribution' => [
                'completed' => Booking::where('status', 'completed')
                    ->whereBetween('booking_date', [$dates['start'], $dates['end']])
                    ->count(),
                'pending' => Booking::where('status', 'pending')
                    ->whereBetween('booking_date', [$dates['start'], $dates['end']])
                    ->count(),
                'cancelled' => Booking::where('status', 'cancelled')
                    ->whereBetween('booking_date', [$dates['start'], $dates['end']])
                    ->count()
            ]
        ];
    }

    private function getTopServices($dates)
    {
        return Service::where('is_active', true)
            ->withCount(['bookings' => function ($query) use ($dates) {
                $query->whereBetween('booking_date', [$dates['start'], $dates['end']])
                    ->where('status', 'completed');
            }])
            ->withSum(['bookings' => function ($query) use ($dates) {
                $query->whereBetween('booking_date', [$dates['start'], $dates['end']])
                    ->where('status', 'completed');
            }], 'total_amount')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get()
            ->map(function ($service) {
                return [
                    'name' => $service->name,
                    'bookings_count' => $service->bookings_count,
                    'total_revenue' => $service->bookings_sum_total_amount ?? 0
                ];
            });
    }

    private function getTopPackages($dates)
    {
        return Package::where('is_active', true)
            ->withCount(['bookings' => function ($query) use ($dates) {
                $query->whereBetween('booking_date', [$dates['start'], $dates['end']])
                    ->where('status', 'completed');
            }])
            ->withSum(['bookings' => function ($query) use ($dates) {
                $query->whereBetween('booking_date', [$dates['start'], $dates['end']])
                    ->where('status', 'completed');
            }], 'total_amount')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get()
            ->map(function ($package) {
                return [
                    'name' => $package->name,
                    'bookings_count' => $package->bookings_count,
                    'total_revenue' => $package->bookings_sum_total_amount ?? 0
                ];
            });
    }
}
