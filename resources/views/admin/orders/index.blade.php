@extends('layouts.admin')

@section('title', 'الطلبات')
@section('page_title', 'إدارة الطلبات')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="orders-container">
                        <!-- Stats Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-primary me-3">
                                                <i class="fas fa-shopping-cart fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">إجمالي الطلبات</h6>
                                                <h3 class="text-white mb-0">{{ $stats['total_orders'] }}</h3>
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
                                                <i class="fas fa-check-circle fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">الطلبات المكتملة</h6>
                                                <h3 class="text-white mb-0">{{ $stats['completed_orders'] }}</h3>
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
                                                <h6 class="text-white mb-1">قيد التنفيذ</h6>
                                                <h3 class="text-white mb-0">{{ $stats['processing_orders'] }}</h3>
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
                                                <h6 class="text-white mb-1">إجمالي المبيعات</h6>
                                                <h3 class="text-white mb-0">{{ number_format($stats['total_revenue'], 2) }} ريال</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1 d-flex align-items-center">
                                                <span class="icon-circle bg-primary text-white me-2">
                                                    <i class="fas fa-shopping-bag"></i>
                                                </span>
                                                إدارة الطلبات
                                            </h5>
                                            <p class="text-muted mb-0 fs-sm">إدارة ومتابعة طلبات العملاء</p>
                                        </div>
                                        <div class="actions d-flex gap-2">
                                            <button type="button" class="btn btn-light-primary btn-wave">
                                                <i class="fas fa-file-export me-2"></i>
                                                تصدير التقرير
                                            </button>
                                            <button type="button" class="btn btn-light-success btn-wave">
                                                <i class="fas fa-print me-2"></i>
                                                طباعة
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>

                        <!-- Search & Filters -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                <div class="card-body">
                                        <form action="{{ route('admin.orders.index') }}" method="GET" id="filters-form">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <div class="search-wrapper">
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-0">
                                                                <i class="fas fa-hashtag text-muted"></i>
                                                            </span>
                                                            <input type="text"
                                                                   name="order_number"
                                                                   class="form-control border-0 shadow-none ps-0"
                                                                   placeholder="البحث برقم الطلب..."
                                                                   value="{{ request('order_number') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="search-wrapper">
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-0">
                                                                <i class="fas fa-search text-muted"></i>
                                                            </span>
                                                            <input type="text"
                                                                   name="search"
                                                                   class="form-control border-0 shadow-none ps-0"
                                                                   placeholder="البحث في العملاء..."
                                                                   value="{{ request('search') }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <select name="order_status" class="form-select border-0 shadow-none bg-light">
                                                        <option value="">كل الحالات</option>
                                                        @foreach($orderStatuses as $value => $label)
                                                            <option value="{{ $value }}" {{ request('order_status') == $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-2">
                                                    <input type="date"
                                                           name="date"
                                                           class="form-control border-0 shadow-none bg-light"
                                                           value="{{ request('date') }}">
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="filter-buttons">
                                                        <button type="submit" class="btn btn-primary btn-wave">
                                                            <i class="fas fa-filter me-2"></i>
                                                            تصفية
                                                        </button>
                                                        @if(request()->hasAny(['order_number', 'search', 'order_status', 'date']))
                                                            <a href="{{ route('admin.orders.index') }}"
                                                               class="btn btn-light-danger btn-wave"
                                                               title="إزالة الفلتر">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orders List -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-0">
                                        <div class="table-wrapper">
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">#</th>
                                                            <th>العميل</th>
                                                            <th>المنتجات</th>
                                                            <th>الإجمالي</th>
                                                            <th>حالة الطلب</th>
                                                            <th>حالة الدفع</th>
                                                            <th>التاريخ</th>
                                                            <th>الإجراءات</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($orders as $order)
                                                        <tr>
                                                            <td class="text-center">{{ $order['order_number'] }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-circle bg-primary text-white me-2">
                                                                        {{ substr($order['customer_name'], 0, 1) }}
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-0">{{ $order['customer_name'] }}</h6>
                                                                        <small class="text-muted">{{ $order['customer_phone'] }}</small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="small">
                                                                    @foreach($order['items'] as $item)
                                                                        <div class="mb-1">
                                                                            {{ $item['product_name'] }}

                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                            <td>{{ number_format($order['total'], 2) }} ريال</td>
                                                            <td>
                                                                <span class="badge bg-{{ $order['status_color'] }}-subtle text-{{ $order['status_color'] }} rounded-pill">
                                                                    {{ $order['status_text'] }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $order['payment_status_color'] }}-subtle text-{{ $order['payment_status_color'] }} rounded-pill">
                                                                    {{ $order['payment_status_text'] }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $order['created_at_formatted'] }}</td>
                                                            <td>
                                                                <div class="action-buttons">
                                                                    <a href="{{ route('admin.orders.show', $order['uuid']) }}"
                                                                       class="btn btn-light-info btn-sm me-2"
                                                                       title="عرض التفاصيل">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center py-5">
                                                                <div class="empty-state">
                                                                    <div class="empty-icon bg-light rounded-circle mb-3">
                                                                        <i class="fas fa-shopping-cart text-muted fa-2x"></i>
                                                                    </div>
                                                                    <h5 class="text-muted mb-0">لا توجد طلبات</h5>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($orders->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="صفحات الطلبات">
                                <ul class="pagination mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($orders->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">
                                                <i class="fas fa-chevron-right"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                        @if ($page == $orders->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($orders->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">
                                                <i class="fas fa-chevron-left"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/orders.css') }}?t={{ time() }}">
@endsection
