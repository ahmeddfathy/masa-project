@extends('layouts.admin')

@section('title', 'تفاصيل الطلب #' . $order->order_number)
@section('page_title', 'تفاصيل الطلب #' . $order->order_number)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="orders-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1 d-flex align-items-center">
                                                <span class="icon-circle bg-primary text-white me-2">
                                                    <i class="fas fa-info-circle"></i>
                                                </span>
                                                تفاصيل الطلب #{{ $order->order_number }}
                                            </h5>
                                            <p class="text-muted mb-0 fs-sm">عرض تفاصيل الطلب والمنتجات والمواعيد</p>
                </div>
                                        <div class="actions d-flex gap-2">
                                            <a href="{{ route('admin.orders.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-2"></i>
                                                عودة للطلبات
                </a>
                                            <button onclick="window.print()" class="btn btn-light-primary">
                                                <i class="fas fa-print me-2"></i>
                    طباعة الطلب
                </button>
            </div>
                            </div>
                        </div>
                    </div>
                </div>

                        <!-- Order Stats -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-primary me-3">
                                                <i class="fas fa-shopping-cart fa-lg"></i>
                        </div>
                                            <div>
                                                <h6 class="text-white mb-1">رقم الطلب</h6>
                                                <h3 class="text-white mb-0">#{{ $order->order_number }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-success h-100">
                <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-success me-3">
                                                <i class="fas fa-box-open fa-lg"></i>
                                    </div>
                                    <div>
                                                <h6 class="text-white mb-1">عدد المنتجات</h6>
                                                <h3 class="text-white mb-0">{{ $order->items->count() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-info h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-info me-3">
                                                <i class="fas fa-money-bill-wave fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">إجمالي الطلب</h6>
                                                <h3 class="text-white mb-0">{{ number_format($order->total_amount) }} ريال</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    </div>
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-warning h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-warning me-3">
                                                <i class="fas fa-clock fa-lg"></i>
                                    </div>
                                    <div>
                                                <h6 class="text-white mb-1">تاريخ الطلب</h6>
                                                <h3 class="text-white mb-0">{{ $order->created_at->format('Y/m/d') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div class="row g-4">
                            <!-- Order Info -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 d-flex align-items-center">
                                            <span class="icon-circle bg-primary text-white me-2">
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                            معلومات الطلب
                                        </h5>
                                        <div class="info-list">
                                            <div class="info-item d-flex justify-content-between py-2">
                                                <span class="text-muted">حالة الطلب</span>
                                                <div>
                                                    <select name="order_status" class="form-select form-select-sm d-inline-block w-auto me-2"
                                                            onchange="this.form.submit()" form="update-status-form">
                                                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                                        <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                                        <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                                    </select>
                                                    <span class="badge bg-{{ $order->status_color }}-subtle text-{{ $order->status_color }} rounded-pill">
                                                        {{ $order->status_text }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="info-item d-flex justify-content-between py-2">
                                                <span class="text-muted">طريقة الدفع</span>
                                                <span>{{ $order->payment_method === 'cash' ? 'كاش' : 'بطاقة' }}</span>
                                            </div>
                                            <div class="info-item d-flex justify-content-between py-2">
                                                <span class="text-muted">حالة الدفع</span>
                                                <div>
                                                    <select name="payment_status" class="form-select form-select-sm d-inline-block w-auto me-2"
                                                            onchange="this.form.submit()" form="update-payment-status-form">
                                                        <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                                        <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>تم الدفع</option>
                                                        <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>فشل الدفع</option>
                                                    </select>
                                                    <span class="badge bg-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }}-subtle
                                                                 text-{{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'pending' ? 'warning' : 'danger') }} rounded-pill">
                                                        {{ $order->payment_status === 'paid' ? 'تم الدفع' : ($order->payment_status === 'pending' ? 'قيد الانتظار' : 'فشل الدفع') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                    </div>
                </div>
                            </div>

                            <!-- Customer Info -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 d-flex align-items-center">
                                            <span class="icon-circle bg-primary text-white me-2">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            معلومات العميل
                                        </h5>
                                        <div class="customer-info">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="avatar-circle bg-primary text-white me-3">
                                                    {{ substr($order->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $order->user->name }}</h6>
                                                    <p class="text-muted mb-0">{{ $order->user->email }}</p>
                                                </div>
                                            </div>
                                            <div class="info-list">
                                                <div class="info-item d-flex align-items-center py-2">
                                                    <i class="fas fa-phone text-primary me-3"></i>
                                                    <span>{{ $order->phone }}</span>
                                                </div>
                                                <div class="info-item d-flex align-items-center py-2">
                                                    <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                                    <span>{{ $order->shipping_address }}</span>
                                                </div>
                                                @if($order->notes)
                                                <div class="info-item d-flex align-items-center py-2">
                                                    <i class="fas fa-sticky-note text-primary me-3"></i>
                                                    <span>{{ $order->notes }}</span>
            </div>
            @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                </div>

                            <!-- Products List -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 d-flex align-items-center">
                                            <span class="icon-circle bg-primary text-white me-2">
                                                <i class="fas fa-shopping-bag"></i>
                                            </span>
                                            منتجات الطلب
                                        </h5>

                                        <!-- Products with Appointments -->
                                        @if($itemsWithAppointments->isNotEmpty())
                                        <div class="table-responsive mb-4">
                                            <h6 class="mb-3">
                                                <i class="fas fa-calendar-check text-primary me-2"></i>
                                                المنتجات مع مواعيد
                                            </h6>
                                            <table class="table table-hover align-middle">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="border-0 text-center" style="width: 60px">#</th>
                                                        <th class="border-0" style="min-width: 250px">المنتج</th>
                                                        <th class="border-0 text-center" style="width: 100px">الكمية</th>
                                                        <th class="border-0" style="width: 100px">اللون</th>
                                                        <th class="border-0" style="width: 100px">المقاس</th>
                                                        <th class="border-0" style="width: 150px">السعر</th>
                                                        <th class="border-0" style="width: 150px">الإجمالي</th>
                                                        <th class="border-0" style="width: 250px">الموعد</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($itemsWithAppointments as $item)
                                                    <tr>
                                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0">
                                                                    @if($item->product->image)
                                                                        <img src="{{ asset($item->product->image) }}"
                                                                             class="product-image border"
                                                                             width="60" height="60"
                                                                             alt="{{ $item->product->name }}">
                                                                    @else
                                                                        <div class="product-image border d-flex align-items-center justify-content-center bg-light">
                                                                            <i class="fas fa-box text-muted fa-lg"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="flex-grow-1 ms-3">
                                                                    <h6 class="mb-1 fw-bold">{{ $item->product->name }}</h6>
                                                                    @if($item->product->category)
                                                                        <span class="badge bg-primary-subtle text-primary">
                                                                            {{ $item->product->category->name }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-light text-dark fw-bold">
                                                                {{ $item->quantity }} قطعة
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($item->color)
                                                                <span class="badge bg-light text-dark">
                                                                    {{ $item->color }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($item->size)
                                                                <span class="badge bg-light text-dark">
                                                                    {{ $item->size }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-money-bill-wave text-success me-2"></i>
                                                                <span class="fw-bold">{{ number_format($item->unit_price) }}</span>
                                                                <small class="text-muted ms-1">ريال</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-calculator text-primary me-2"></i>
                                                                <span class="fw-bold">{{ number_format($item->subtotal) }}</span>
                                                                <small class="text-muted ms-1">ريال</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($item->appointment)
                                                                <div class="appointment-card">
                                                                    <div class="appointment-header">
                                                                        <span class="appointment-date">
                                                                            <i class="fas fa-calendar-alt text-info me-2"></i>
                                                                            {{ $item->appointment->appointment_date->format('Y/m/d') }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="appointment-status mt-2">
                                                                        @if($item->appointment->status === 'approved')
                                                                            <div class="status-badge status-approved">
                                                                                <i class="fas fa-check-circle"></i>
                                                                                تم التأكيد
                                                                            </div>
                                                                        @else
                                                                            <div class="status-badge status-pending">
                                                                                <i class="fas fa-clock"></i>
                                                                                قيد الانتظار
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif

                                        <!-- Products without Appointments -->
                                        @if($itemsWithoutAppointments->isNotEmpty())
                                        <div class="table-responsive">
                                            <h6 class="mb-3">
                                                <i class="fas fa-shopping-bag text-primary me-2"></i>
                                                المنتجات بدون مواعيد
                                            </h6>
                                            <table class="table table-hover align-middle">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="border-0 text-center" style="width: 60px">#</th>
                                                        <th class="border-0" style="min-width: 250px">المنتج</th>
                                                        <th class="border-0 text-center" style="width: 100px">الكمية</th>
                                                        <th class="border-0" style="width: 100px">اللون</th>
                                                        <th class="border-0" style="width: 100px">المقاس</th>
                                                        <th class="border-0" style="width: 150px">السعر</th>
                                                        <th class="border-0" style="width: 150px">الإجمالي</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($itemsWithoutAppointments as $item)
                                                    <tr>
                                                        <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0">
                                                                    @if($item->product->image)
                                                                        <img src="{{ asset($item->product->image) }}"
                                                                             class="product-image border"
                                                                             width="60" height="60"
                                                                             alt="{{ $item->product->name }}">
                                                                    @else
                                                                        <div class="product-image border d-flex align-items-center justify-content-center bg-light">
                                                                            <i class="fas fa-box text-muted fa-lg"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="flex-grow-1 ms-3">
                                                                    <h6 class="mb-1 fw-bold">{{ $item->product->name }}</h6>
                                                                    @if($item->product->category)
                                                                        <span class="badge bg-primary-subtle text-primary">
                                                                            {{ $item->product->category->name }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-light text-dark fw-bold">
                                                                {{ $item->quantity }} قطعة
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($item->color)
                                                                <span class="badge bg-light text-dark">
                                                                    {{ $item->color }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($item->size)
                                                                <span class="badge bg-light text-dark">
                                                                    {{ $item->size }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-money-bill-wave text-success me-2"></i>
                                                                <span class="fw-bold">{{ number_format($item->unit_price) }}</span>
                                                                <small class="text-muted ms-1">ريال</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-calculator text-primary me-2"></i>
                                                                <span class="fw-bold">{{ number_format($item->subtotal) }}</span>
                                                                <small class="text-muted ms-1">ريال</small>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif

                                        @if($itemsWithAppointments->isEmpty() && $itemsWithoutAppointments->isEmpty())
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-shopping-cart mb-2 fa-2x"></i>
                                            <p class="mb-0">لا توجد منتجات في هذا الطلب</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Contact Information -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title mb-4 d-flex align-items-center">
                                        <span class="icon-circle bg-primary text-white me-2">
                                            <i class="fas fa-address-book"></i>
                                        </span>
                                        معلومات الاتصال الإضافية
                                    </h5>

                                    @if($additionalAddresses->isNotEmpty())
                                    <div class="mb-4">
                                        <h6 class="mb-3">العناوين الإضافية</h6>
                                        <div class="row g-3">
                                            @foreach($additionalAddresses as $address)
                                            <div class="col-md-6">
                                                <div class="address-card bg-light p-3 rounded">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                        <span class="fw-bold">{{ $address->type_text }}</span>
                                                    </div>
                                                    <p class="mb-0">{{ $address->full_address }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    @if($additionalPhones->isNotEmpty())
                                    <div>
                                        <h6 class="mb-3">أرقام الهواتف الإضافية</h6>
                                        <div class="row g-3">
                                            @foreach($additionalPhones as $phone)
                                            <div class="col-md-4">
                                                <div class="phone-card bg-light p-3 rounded">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-phone text-primary me-2"></i>
                                                        <div>
                                                            <div class="fw-bold">{{ $phone->phone }}</div>
                                                            <small class="text-muted">{{ $phone->type_text }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                    @if($additionalAddresses->isEmpty() && $additionalPhones->isEmpty())
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle mb-2 fa-2x"></i>
                                        <p class="mb-0">لا توجد معلومات اتصال إضافية</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms for Status Updates -->
<form id="update-status-form" action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-none">
    @csrf
    @method('PUT')
</form>

<form id="update-payment-status-form" action="{{ route('admin.orders.update-payment-status', $order) }}" method="POST" class="d-none">
    @csrf
    @method('PUT')
</form>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/orders.css') }}">
@endsection
