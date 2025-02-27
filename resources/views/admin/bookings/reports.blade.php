@extends('layouts.admin')

@section('styles')
<style>
    .stat-card {
        border-radius: 15px;
        transition: transform 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
<link rel="stylesheet" href="/assets/css/admin/bookings.css">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تقارير الحجوزات</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">إجمالي الحجوزات</span>
                                    <span class="info-box-number">{{ $stats['total_bookings'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">حجوزات معلقة</span>
                                    <span class="info-box-number">{{ $stats['pending_bookings'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">حجوزات مكتملة</span>
                                    <span class="info-box-number">{{ $stats['completed_bookings'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">إجمالي الإيرادات</span>
                                    <span class="info-box-number">{{ $stats['total_revenue'] }} درهم</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- يمكن إضافة المزيد من الإحصائيات والرسوم البيانية هنا -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // رسم بياني للحجوزات الشهرية
    const monthlyData = @json($monthlyBookings);
    const monthlyChart = new Chart(document.getElementById('monthlyBookingsChart'), {
        type: 'line',
        data: {
            labels: monthlyData.map(item => {
                return new Date(2024, item.month - 1).toLocaleDateString('ar', { month: 'long' });
            }),
            datasets: [{
                label: 'عدد الحجوزات',
                data: monthlyData.map(item => item.count),
                borderColor: '#4e73df',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // رسم بياني دائري لحالات الحجوزات
    const statusChart = new Chart(document.getElementById('bookingStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['قيد الانتظار', 'مؤكدة', 'مكتملة', 'ملغية'],
            datasets: [{
                data: [
                    {{ $pendingBookings }},
                    {{ $confirmedBookings }},
                    {{ $completedBookings }},
                    {{ $cancelledBookings }}
                ],
                backgroundColor: ['#ffc107', '#28a745', '#17a2b8', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endsection
