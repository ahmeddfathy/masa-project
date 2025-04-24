<?php
// Calculate coupon status class
$statusClass = '';
$statusText = '';

if (!$coupon->is_active) {
    $statusClass = 'text-secondary';
    $statusText = 'غير نشط';
} elseif ($coupon->expires_at && $coupon->expires_at->isPast()) {
    $statusClass = 'text-danger';
    $statusText = 'منتهي';
} else {
    $statusClass = 'text-success';
    $statusText = 'نشط';
}

// Calculate coupon type details
$typeClass = $coupon->type === 'percentage' ? 'text-primary' : 'text-info';
$typeIcon = $coupon->type === 'percentage' ? 'percent' : 'tag';
$typeText = $coupon->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت';
?>

@extends('layouts.admin')

@section('title', 'تفاصيل الكوبون: ' . $coupon->code)
@section('page_title', 'تفاصيل الكوبون: ' . $coupon->code)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="coupons-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-tags text-primary me-2"></i>
                                            تفاصيل الكوبون
                                            <span class="coupon-code ms-2">{{ $coupon->code }}</span>
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للكوبونات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Coupon Header -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="fs-4 fw-bold mb-0 d-flex align-items-center">
                                                    {{ $coupon->name }}
                                                    <span class="badge {{ $statusClass === 'text-success' ? 'bg-success' : ($statusClass === 'text-danger' ? 'bg-danger' : 'bg-secondary') }} ms-3">
                                                        {{ $statusText }}
                                                    </span>
                                                </h4>
                                                <p class="text-muted mb-0 mt-2">
                                                    <i class="fas fa-{{ $typeIcon }} {{ $typeClass }} me-1"></i>
                                                    {{ $typeText }}:
                                                    <strong class="{{ $typeClass }}">{{ $coupon->type === 'percentage' ? $coupon->value . '%' : number_format($coupon->value, 2) . ' ر.س' }}</strong>
                                                </p>
                                            </div>
                                            <div class="coupon-usage-info text-center">
                                                <span class="d-block fs-4 fw-bold {{ $coupon->max_uses && $coupon->used_count >= $coupon->max_uses ? 'text-danger' : 'text-dark' }}">
                                                    {{ $coupon->used_count }}
                                                    @if($coupon->max_uses)
                                                        <span class="text-muted fs-6"> / {{ $coupon->max_uses }}</span>
                                                    @endif
                                                </span>
                                                <span class="d-block text-muted small">عدد الاستخدامات</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Coupon Details -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-12 d-flex justify-content-between align-items-center">
                                                <h4 class="fs-4 fw-bold mb-0">{{ $coupon->name }}</h4>
                                                <div>
                                                    @if($coupon->is_active)
                                                        <span class="badge bg-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-secondary">غير نشط</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <!-- Basic Information -->
                                            <div class="col-md-6">
                                                <div class="card border-0 shadow-sm mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title mb-3">
                                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                                            معلومات الكوبون الأساسية
                                                        </h5>

                                                        <div class="row g-3">
                                                            <div class="col-12">
                                                                <div class="detail-item">
                                                                    <label>كود الكوبون:</label>
                                                                    <p class="mb-0 fs-5"><span class="coupon-code">{{ $coupon->code }}</span></p>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <div class="detail-item">
                                                                    <label>نوع الخصم:</label>
                                                                    <p class="mb-0">
                                                                        @if ($coupon->type === 'percentage')
                                                                            <span class="badge bg-primary-subtle text-primary">نسبة مئوية ({{ $coupon->value }}%)</span>
                                                                        @else
                                                                            <span class="badge bg-info-subtle text-info">مبلغ ثابت ({{ number_format($coupon->value, 2) }} ر.س)</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <div class="detail-item">
                                                                    <label>الحد الأدنى للطلب:</label>
                                                                    <p class="mb-0">{{ number_format($coupon->min_order_amount, 2) }} ر.س</p>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <div class="detail-item">
                                                                    <label>عدد الاستخدامات:</label>
                                                                    <p class="mb-0">
                                                                        <span class="badge bg-light text-dark">{{ $coupon->used_count }}</span>
                                                                        @if($coupon->max_uses)
                                                                            <span class="text-muted"> / {{ $coupon->max_uses }}</span>
                                                                        @else
                                                                            <span class="text-muted">(بدون حد أقصى)</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Dates and Application -->
                                            <div class="col-md-6">
                                                <div class="card border-0 shadow-sm mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title mb-3">
                                                            <i class="fas fa-calendar text-primary me-2"></i>
                                                            فترة الصلاحية والتطبيق
                                                        </h5>

                                                        <div class="row g-3">
                                                            <div class="col-12">
                                                                <div class="detail-item">
                                                                    <label>تاريخ البدء:</label>
                                                                    <p class="mb-0">
                                                                        @if($coupon->starts_at)
                                                                            {{ $coupon->starts_at->format('Y-m-d H:i') }}
                                                                        @else
                                                                            <span class="text-muted">غير محدد (مفعل فوراً)</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <div class="detail-item">
                                                                    <label>تاريخ الانتهاء:</label>
                                                                    <p class="mb-0">
                                                                        @if($coupon->expires_at)
                                                                            @if($coupon->expires_at->isPast())
                                                                                <span class="badge bg-danger">منتهي ({{ $coupon->expires_at->format('Y-m-d H:i') }})</span>
                                                                            @else
                                                                                {{ $coupon->expires_at->format('Y-m-d H:i') }}
                                                                            @endif
                                                                        @else
                                                                            <span class="text-muted">غير محدد (غير منتهي)</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <div class="detail-item">
                                                                    <label>ينطبق على:</label>
                                                                    <p class="mb-0">
                                                                        @if($coupon->applies_to_products)
                                                                            @if($coupon->applies_to_all_products)
                                                                                <span class="badge bg-success">جميع المنتجات</span>
                                                                            @else
                                                                                <span class="badge bg-warning text-dark">منتجات محددة ({{ $coupon->products->count() }})</span>
                                                                            @endif
                                                                        @else
                                                                            <span class="badge bg-secondary">لا ينطبق على المنتجات</span>
                                                                        @endif

                                                                        @if($coupon->applies_to_packages)
                                                                            <span class="badge bg-info ms-1">باقات الاستوديو ({{ $coupon->packages->count() }})</span>
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <div class="detail-item">
                                                                    <label>تاريخ الإنشاء:</label>
                                                                    <p class="mb-0">{{ $coupon->created_at->format('Y-m-d H:i') }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @if($coupon->description)
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm mb-4">
                                                        <div class="card-body">
                                                            <h5 class="card-title mb-3">
                                                                <i class="fas fa-align-left text-primary me-2"></i>
                                                                وصف الكوبون
                                                            </h5>
                                                            <p class="mb-0">{{ $coupon->description }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($coupon->applies_to_products && !$coupon->applies_to_all_products && $coupon->products->count() > 0)
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm mb-4">
                                                        <div class="card-body">
                                                            <h5 class="card-title mb-3">
                                                                <i class="fas fa-shopping-cart text-primary me-2"></i>
                                                                المنتجات المرتبطة
                                                            </h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-striped table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>اسم المنتج</th>
                                                                            <th>السعر</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($coupon->products as $product)
                                                                            <tr>
                                                                                <td>{{ $product->name }}</td>
                                                                                <td>{{ number_format($product->price, 2) }} ر.س</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if($coupon->applies_to_packages && $coupon->packages->count() > 0)
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card border-0 shadow-sm mb-4">
                                                        <div class="card-body">
                                                            <h5 class="card-title mb-3">
                                                                <i class="fas fa-box text-primary me-2"></i>
                                                                الباقات المرتبطة
                                                            </h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-striped table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>اسم الباقة</th>
                                                                            <th>السعر</th>
                                                                            <th>الخدمات</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($coupon->packages as $package)
                                                                            <tr>
                                                                                <td>{{ $package->name }}</td>
                                                                                <td>{{ number_format($package->base_price, 2) }} ر.س</td>
                                                                                <td>
                                                                                    @if($package->services->count() > 0)
                                                                                        @foreach($package->services as $service)
                                                                                            <span class="badge bg-info-subtle text-info me-1">{{ $service->name }}</span>
                                                                                        @endforeach
                                                                                    @else
                                                                                        <span class="text-muted">-</span>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Actions -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body d-flex justify-content-end">
                                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary me-2">
                                                            <i class="fas fa-edit me-1"></i>
                                                            تعديل الكوبون
                                                        </a>
                                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                                                                <i class="fas fa-trash-alt me-1"></i>
                                                                حذف الكوبون
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/coupon.css') }}?t={{ time() }}">
@endsection
