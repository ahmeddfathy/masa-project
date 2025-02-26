@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('page_title', 'لوحة التحكم')

@section('content')
<!-- Stats Grid -->
<div class="container-fluid">
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        {{ session('warning') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Stats Grid -->
    <div class="row g-3 mb-4">
        <!-- Orders Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper bg-primary me-3">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value h4 mb-1">{{ $stats['orders'] ?? 0 }}</div>
                        <div class="stat-title text-muted">إجمالي الطلبات</div>
                        <div class="trend small mt-2">
                            <span class="me-2">اليوم: {{ $stats['today_orders'] ?? 0 }}</span>
                            <span>الشهر: {{ $stats['month_orders'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper bg-success me-3">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value h4 mb-1">{{ $stats['revenue'] }} ريال</div>
                        <div class="stat-title text-muted">إجمالي الإيرادات</div>
                        <div class="trend small mt-2">
                            <span>اليوم: {{ $stats['today_revenue'] }} ريال</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper bg-info me-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value h4 mb-1">{{ $stats['users'] }}</div>
                        <div class="stat-title text-muted">إجمالي المستخدمين</div>
                        <div class="trend small mt-2">
                            <span>المستخدمين النشطين</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper bg-warning me-3">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value h4 mb-1">{{ $stats['pending_orders'] ?? 0 }}</div>
                        <div class="stat-title text-muted">الطلبات المعلقة</div>
                        <div class="trend small mt-2">
                            <span class="me-2">قيد المعالجة: {{ $stats['processing_orders'] ?? 0 }}</span>
                            <span>مكتملة: {{ $stats['completed_orders'] ?? 0 }}</span>
                        </div>
                        <div class="trend small mt-1">
                            <span class="me-2">قيد التوصيل: {{ $stats['out_for_delivery_orders'] ?? 0 }}</span>
                            <span>في الطريق: {{ $stats['on_the_way_orders'] ?? 0 }}</span>
                        </div>
                        <div class="trend small mt-1">
                            <span class="me-2">تم التوصيل: {{ $stats['delivered_orders'] ?? 0 }}</span>
                            <span>مرتجع: {{ $stats['returned_orders'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Studio Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h3 class="mb-3">إحصائيات الاستوديو</h3>
        </div>

        <!-- Studio Bookings Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper bg-primary me-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value h4 mb-1">{{ $stats['total_bookings'] ?? 0 }}</div>
                        <div class="stat-title text-muted">إجمالي الحجوزات</div>
                        <div class="trend small mt-2">
                            <span class="me-2">اليوم: {{ $stats['today_bookings'] ?? 0 }}</span>
                            <span>الشهر: {{ $stats['month_bookings'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Studio Revenue Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper bg-success me-3">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value h4 mb-1">{{ number_format($stats['studio_revenue'] ?? 0) }} ريال</div>
                        <div class="stat-title text-muted">إيرادات الاستوديو</div>
                        <div class="trend small mt-2">
                            <span class="me-2">اليوم: {{ number_format($stats['today_studio_revenue'] ?? 0) }} ريال</span>
                            <span>الشهر: {{ number_format($stats['month_studio_revenue'] ?? 0) }} ريال</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Studio Services Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper bg-info me-3">
                        <i class="fas fa-camera-retro"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value h4 mb-1">{{ $stats['total_services'] }}</div>
                        <div class="stat-title text-muted">الخدمات المتوفرة</div>
                        <div class="trend small mt-2">
                            <span class="me-2">الباقات: {{ $stats['total_packages'] }}</span>
                            <span>الإضافات: {{ $stats['total_addons'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Studio Bookings Status Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card h-100 p-3 bg-white rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper bg-warning me-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value h4 mb-1">{{ $bookingStats['pending'] ?? 0 }}</div>
                        <div class="stat-title text-muted">الحجوزات المعلقة</div>
                        <div class="trend small mt-2">
                            <span class="me-2">مكتملة: {{ $bookingStats['completed'] ?? 0 }}</span>
                            <span>ملغية: {{ $bookingStats['cancelled'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 mb-4">
        <!-- Store Management -->
        <div class="col-12">
            <h3 class="section-title mb-3">إدارة المتجر</h3>
        </div>
        @can('create', App\Models\Product::class)
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('admin.products.create') }}" class="action-card-link">
                <div class="action-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-start">
                        <div class="action-icon bg-gradient-primary me-4">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="action-content">
                            <h5 class="mb-2 text-dark fw-bold">إضافة منتج</h5>
                            <p class="mb-0 text-muted">إضافة منتجات جديدة للمتجر</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcan

        @can('viewAny', App\Models\Order::class)
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('admin.orders.index') }}" class="action-card-link">
                <div class="action-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-start">
                        <div class="action-icon bg-gradient-info me-4">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="action-content">
                            <h5 class="mb-2 text-dark fw-bold">إدارة الطلبات</h5>
                            <p class="mb-0 text-muted">عرض وإدارة طلبات العملاء</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endcan

        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('admin.reports.index') }}" class="action-card-link">
                <div class="action-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-start">
                        <div class="action-icon bg-gradient-success me-4">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="action-content">
                            <h5 class="mb-2 text-dark fw-bold">تقارير المتجر</h5>
                            <p class="mb-0 text-muted">إحصائيات وتحليلات المبيعات</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Studio Management -->
        <div class="col-12">
            <h3 class="section-title mb-3 mt-4">إدارة الاستوديو</h3>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('admin.gallery.index') }}" class="action-card-link">
                <div class="action-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-start">
                        <div class="action-icon bg-gradient-purple me-4">
                            <i class="far fa-images"></i>
                        </div>
                        <div class="action-content">
                            <h5 class="mb-2 text-dark fw-bold">معرض الصور</h5>
                            <p class="mb-0 text-muted">إدارة معرض صور الاستوديو</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('admin.services.index') }}" class="action-card-link">
                <div class="action-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-start">
                        <div class="action-icon bg-gradient-blue me-4">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="action-content">
                            <h5 class="mb-2 text-dark fw-bold">الخدمات</h5>
                            <p class="mb-0 text-muted">إدارة خدمات الاستوديو</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('admin.packages.index') }}" class="action-card-link">
                <div class="action-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-start">
                        <div class="action-icon bg-gradient-pink me-4">
                            <i class="far fa-star"></i>
                        </div>
                        <div class="action-content">
                            <h5 class="mb-2 text-dark fw-bold">الباقات</h5>
                            <p class="mb-0 text-muted">إدارة باقات التصوير</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('admin.addons.index') }}" class="action-card-link">
                <div class="action-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-start">
                        <div class="action-icon bg-gradient-pink me-4">
                            <i class="fas fa-puzzle-piece"></i>
                        </div>
                        <div class="action-content">
                            <h5 class="mb-2 text-dark fw-bold">الخدمات الإضافية</h5>
                            <p class="mb-0 text-muted">إدارة الخدمات الإضافية</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('admin.bookings.calendar') }}" class="action-card-link">
                <div class="action-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-start">
                        <div class="action-icon bg-gradient-green me-4">
                            <i class="far fa-calendar-check"></i>
                        </div>
                        <div class="action-content">
                            <h5 class="mb-2 text-dark fw-bold">تقويم الحجوزات</h5>
                            <p class="mb-0 text-muted">عرض وإدارة مواعيد الحجوزات</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('admin.studio-reports.index') }}" class="action-card-link">
                <div class="action-card bg-white rounded-3 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-start">
                        <div class="action-icon bg-gradient-orange me-4">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="action-content">
                            <h5 class="mb-2 text-dark fw-bold">تقارير الاستوديو</h5>
                            <p class="mb-0 text-muted">إحصائيات وتحليلات الحجوزات</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts & Tables -->
    <div class="row g-4">
        <!-- Sales Chart -->
        <div class="col-12 col-lg-8">
            <div class="chart-container bg-white rounded-3 shadow-sm">
                <div class="activity-header border-bottom">
                    <h5 class="activity-title">نظرة عامة على المبيعات والحجوزات</h5>
                </div>
                <div class="chart-wrapper position-relative" style="height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Booking Status Chart -->
        <div class="col-12 col-lg-4">
            <div class="chart-container bg-white rounded-3 shadow-sm">
                <div class="activity-header border-bottom">
                    <h5 class="activity-title">توزيع حالات الحجوزات</h5>
                </div>
                <div class="chart-wrapper position-relative" style="height: 300px;">
                    <canvas id="bookingStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="activity-section bg-white rounded-3 shadow-sm">
                <div class="activity-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="activity-title mb-0">آخر الطلبات</h5>
                    @can('viewAny', App\Models\Order::class)
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                    @endcan
                </div>
                <div class="table-responsive-xl">
                    <table class="table table-hover mb-0 recent-orders-table">
                        <thead>
                            <tr>
                                <th style="min-width: 70px">الطلب</th>
                                <th style="min-width: 120px">العميل</th>
                                <th style="min-width: 250px">المنتجات</th>
                                <th style="min-width: 120px">حالة الطلب</th>
                                <th style="min-width: 120px">حالة الدفع</th>
                                <th style="min-width: 100px">المبلغ</th>
                                <th style="min-width: 120px">التاريخ</th>
                                <th style="min-width: 80px">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td data-label="الطلب">#{{ $order['order_number'] }}</td>
                                <td data-label="العميل">{{ $order['user_name'] }}</td>
                                <td data-label="المنتجات">
                                    <div class="small products-list">
                                        @foreach($order['items'] as $item)
                                            <div class="mb-1">
                                                {{ $item['product_name'] }}
                                                <span class="text-muted d-block d-md-inline">
                                                    ({{ $item['quantity'] }} × {{ $item['unit_price'] }} ريال = {{ $item['total_price'] }} ريال)
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td data-label="حالة الطلب">
                                    <span class="badge bg-{{ match($order['order_status']) {
                                        'completed' => 'success',
                                        'processing' => 'info',
                                        'pending' => 'warning',
                                        'cancelled' => 'danger',
                                        'out_for_delivery' => 'primary',
                                        'on_the_way' => 'info',
                                        'delivered' => 'success',
                                        'returned' => 'secondary',
                                        default => 'secondary'
                                    } }}">
                                        {{ match($order['order_status']) {
                                            'completed' => 'مكتمل',
                                            'processing' => 'قيد المعالجة',
                                            'pending' => 'معلق',
                                            'cancelled' => 'ملغي',
                                            'out_for_delivery' => 'قيد التوصيل',
                                            'on_the_way' => 'في الطريق',
                                            'delivered' => 'تم التوصيل',
                                            'returned' => 'مرتجع',
                                            default => 'غير معروف'
                                        } }}
                                    </span>
                                </td>
                                <td data-label="حالة الدفع">
                                    <span class="badge bg-{{ $order['payment_status_color'] }}">
                                        {{ $order['payment_status_text'] }}
                                    </span>
                                </td>
                                <td data-label="المبلغ">{{ $order['total'] }} ريال</td>
                                <td data-label="التاريخ">{{ $order['created_at'] }}</td>
                                <td data-label="الإجراءات">
                                    <a href="{{ route('admin.orders.show', $order['uuid']) }}"
                                       class="btn btn-sm btn-primary"
                                       title="عرض تفاصيل الطلب">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-3">لا توجد طلبات حديثة</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row g-4 mt-3">
        <div class="col-12">
            <div class="activity-section bg-white rounded-3 shadow-sm">
                <div class="activity-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="activity-title mb-0">آخر الحجوزات</h5>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="table-responsive-xl">
                    <table class="table table-hover mb-0 recent-bookings-table">
                        <thead>
                            <tr>
                                <th style="min-width: 70px">الحجز</th>
                                <th style="min-width: 120px">العميل</th>
                                <th style="min-width: 250px">الباقة والإضافات</th>
                                <th style="min-width: 120px">موعد الحجز</th>
                                <th style="min-width: 120px">حالة الحجز</th>
                                <th style="min-width: 120px">حالة الدفع</th>
                                <th style="min-width: 100px">المبلغ</th>
                                <th style="min-width: 80px">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td data-label="الحجز">#{{ $booking['booking_number'] }}</td>
                                <td data-label="العميل">{{ $booking['user_name'] }}</td>
                                <td data-label="التفاصيل">
                                    <div class="small products-list">
                                        <div class="mb-1">
                                            <strong>الباقة:</strong> {{ $booking['package_name'] }}
                                        </div>
                                        @if(count($booking['addons']) > 0)
                                            <div class="mt-1">
                                                <strong>الإضافات:</strong>
                                                @foreach($booking['addons'] as $addon)
                                                    <div class="mt-1">
                                                        {{ $addon['name'] }} ({{ $addon['price'] }} ريال)
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="الموعد">
                                    <div>{{ $booking['booking_date'] }}</div>
                                    <div class="small text-muted">{{ $booking['time_slot'] }}</div>
                                </td>
                                <td data-label="حالة الحجز">
                                    <span class="badge bg-{{ $booking['status_color'] }}">
                                        {{ $booking['status_text'] }}
                                    </span>
                                </td>
                                <td data-label="حالة الدفع">
                                    <span class="badge bg-{{ $booking['payment_status_color'] }}">
                                        {{ $booking['payment_status_text'] }}
                                    </span>
                                </td>
                                <td data-label="المبلغ">{{ $booking['total'] }} ريال</td>
                                <td data-label="الإجراءات">
                                    <a href="{{ route('admin.bookings.show', $booking['uuid']) }}"
                                       class="btn btn-sm btn-primary"
                                       title="عرض تفاصيل الحجز">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-3">لا توجد حجوزات حديثة</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js"></script>

<script>
    // دالة تسجيل للمساعدة في تتبع الأخطاء
    function log(message) {
        console.log(`[Admin Dashboard] ${message}`);
    }

    // تهيئة Firebase
    try {
        firebase.initializeApp({
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        });
        log('Firebase initialized successfully');
    } catch (error) {
        log('Firebase initialization error: ' + error.message);
    }

    // تهيئة خدمة الرسائل
    const messaging = firebase.messaging();
    log('Messaging service initialized');

    // دالة طلب الإذن والحصول على التوكن
    async function requestPermissionAndToken() {
        try {
            log('Checking Service Worker support...');
            if (!('serviceWorker' in navigator)) {
                throw new Error('Service Worker not supported');
            }
            if (!('PushManager' in window)) {
                throw new Error('Push notifications not supported');
            }

            log('Requesting notification permission...');
            const permission = await Notification.requestPermission();
            log('Permission: ' + permission);

            if (permission === 'granted') {
                log('Registering Service Worker...');
                const registration = await navigator.serviceWorker.register('/admin/firebase-messaging-sw.js');
                log('Service Worker registered successfully');

                try {
                    log('Setting up messaging service worker...');
                    messaging.useServiceWorker(registration);

                    log('Requesting FCM token...');
                    const currentToken = await messaging.getToken();

                    if (currentToken) {
                        log('FCM Token received: ' + currentToken);
                        updateFcmToken(currentToken);
                        return currentToken;
                    } else {
                        log('No registration token available');
                        return null;
                    }
                } catch (tokenError) {
                    log('Token error: ' + tokenError.message);
                    return null;
                }
            }
        } catch (err) {
            log('Permission/Token error: ' + err.message);
            return null;
        }
    }

    // معالجة الرسائل في الواجهة الأمامية
    messaging.onMessage((payload) => {
        log('Message received in foreground: ' + JSON.stringify(payload));

        try {
            log('Attempting to show direct notification...');
            const notification = new Notification(payload.notification.title, {
                body: payload.notification.body,
                vibrate: [100, 50, 100],
                requireInteraction: true,
                dir: 'rtl',
                lang: 'ar',
                tag: Date.now().toString(),
                data: payload.data
            });

            notification.onclick = function(event) {
                event.preventDefault();
                window.focus();
                notification.close();

                // التوجيه إلى صفحة الطلب إذا كان هناك رابط
                if (payload.data && payload.data.link) {
                    window.location.href = payload.data.link;
                }

                // تحديث الصفحة إذا كنا في صفحة الطلبات
                if (window.location.pathname.includes('/admin/orders')) {
                    window.location.reload();
                }
            };

            notification.onclose = function() {
                log('Notification closed');
            };

            log('Direct notification shown successfully');
        } catch (error) {
            log('Direct notification error: ' + error.message);

            // استخدام Service Worker كخطة بديلة
            if ('serviceWorker' in navigator && 'PushManager' in window) {
                navigator.serviceWorker.ready.then(registration => {
                    return registration.showNotification(payload.notification.title, {
                        body: payload.notification.body,
                        vibrate: [100, 50, 100],
                        requireInteraction: true,
                        dir: 'rtl',
                        lang: 'ar',
                        tag: Date.now().toString(),
                        data: payload.data
                    });
                }).then(() => {
                    log('Notification shown via Service Worker');
                }).catch(error => {
                    log('Service Worker notification error: ' + error.message);
                });
            }
        }
    });

    // تحديث FCM token في قاعدة البيانات
    function updateFcmToken(token) {
        fetch('{{ route("admin.update-fcm-token") }}', {  // استخدام route helper
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'  // إضافة header للقبول
            },
            body: JSON.stringify({ fcm_token: token })  // تغيير اسم المفتاح
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            log('Token updated successfully');
        })
        .catch(error => {
            log('Token update error: ' + error.message);
        });
    }

    // بدء العملية عند تحميل الصفحة
    requestPermissionAndToken();

    // تهيئة البيانات
    const chartConfig = {
        labels: JSON.parse('@json($chartLabels)'),
        data: JSON.parse('@json($chartData)'),
        studioData: JSON.parse('@json($studioChartData)'),
        orderStats: JSON.parse('@json($orderStats)'),
        bookingStats: JSON.parse('@json($bookingStats)')
    };

    // رسم بياني للمبيعات والحجوزات
    const salesChart = new Chart(
        document.getElementById('salesChart').getContext('2d'),
        {
            type: 'bar',
            data: {
                labels: chartConfig.labels,
                datasets: [
                    {
                        label: 'مبيعات المتجر (ريال)',
                        data: chartConfig.data,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgb(13, 110, 253)',
                        borderWidth: 2,
                        borderRadius: 5,
                        barThickness: 20
                    },
                    {
                        label: 'إيرادات الاستوديو (ريال)',
                        data: chartConfig.studioData,
                        backgroundColor: 'rgba(255, 105, 180, 0.2)',
                        borderColor: 'rgb(255, 105, 180)',
                        borderWidth: 2,
                        borderRadius: 5,
                        barThickness: 20
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + ' ريال';
                            },
                            font: {
                                size: 12
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' ريال';
                            }
                        }
                    }
                }
            }
        }
    );

    // رسم بياني لحالات الحجوزات
    const bookingStatusChart = new Chart(
        document.getElementById('bookingStatusChart').getContext('2d'),
        {
            type: 'doughnut',
            data: {
                labels: ['مكتمل', 'معلق', 'ملغي'],
                datasets: [{
                    data: [
                        chartConfig.bookingStats.completed || 0,
                        chartConfig.bookingStats.pending || 0,
                        chartConfig.bookingStats.cancelled || 0
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.9)',
                        'rgba(255, 193, 7, 0.9)',
                        'rgba(220, 53, 69, 0.9)'
                    ],
                    borderColor: [
                        'rgb(25, 135, 84)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 13
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${value} حجز (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        }
    );
</script>

@if(isset($error))
<script>
    // عرض رسالة الخطأ إذا وجدت
    Swal.fire({
        title: 'خطأ!',
        text: '{{ $error }}',
        icon: 'error',
        confirmButtonText: 'حسناً'
    });
</script>
@endif
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/admin-dashboard.css') }}">
<style>
    /* Dashboard Cards */
    .stat-card {
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-3px);
    }

    .icon-wrapper {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        color: white;
        font-size: 1.25rem;
    }

    .action-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
        color: white;
        font-size: 1.4rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .action-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }

    .action-card:hover .action-icon {
        transform: scale(1.1);
    }

    .action-content {
        flex: 1;
    }

    .action-content h5 {
        font-size: 1.1rem;
        line-height: 1.4;
    }

    .action-content p {
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* Chart Styles */
    .chart-container {
        width: 100%;
        height: 100%;
        min-height: 350px;
    }

    .chart-wrapper {
        width: 100%;
        padding: 1rem;
        height: 100%;
    }

    /* Table Styles */
    .recent-orders-table {
        width: 100%;
    }

    .products-list {
        max-width: 100%;
    }

    /* Responsive Styles */
    @media (max-width: 767.98px) {
        .icon-wrapper {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .action-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }

        .stat-value {
            font-size: 1.25rem;
        }

        .trend {
            font-size: 0.75rem;
        }

        .chart-container {
            min-height: 300px;
        }

        .table-responsive-xl {
            border: 0;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .products-list {
            max-width: 250px;
        }

        .products-list .text-muted {
            font-size: 0.85em;
        }
    }

    @media (min-width: 768px) and (max-width: 991.98px) {
        .chart-container {
            min-height: 325px;
        }
    }

    @media (min-width: 992px) {
        .chart-container {
            min-height: 350px;
        }
    }

    /* Gradient Backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(45deg, #0d6efd, #0a58ca);
    }

    .bg-gradient-info {
        background: linear-gradient(45deg, #0dcaf0, #0aa2c0);
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, #198754, #146c43);
    }

    .bg-gradient-purple {
        background: linear-gradient(45deg, #6f42c1, #8a63d2);
    }

    .bg-gradient-blue {
        background: linear-gradient(45deg, #0d6efd, #3d8bfd);
    }

    .bg-gradient-pink {
        background: linear-gradient(45deg, #d63384, #e558a6);
    }

    .bg-gradient-green {
        background: linear-gradient(45deg, #198754, #28a745);
    }

    .bg-gradient-orange {
        background: linear-gradient(45deg, #fd7e14, #ff9843);
    }

    .action-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .action-card-link:hover {
        text-decoration: none;
        color: inherit;
    }
</style>
@endsection
