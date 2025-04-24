@extends('layouts.admin')

@section('title', 'كوبونات الخصم')
@section('page_title', 'كوبونات الخصم')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="coupons-container">
                        <!-- Header & Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
                                <div class="card stat-card bg-gradient-primary text-white border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="icon-circle text-white">
                                            <i class="fas fa-tags fa-fw"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">إجمالي الكوبونات</h6>
                                            <h3 class="mb-0">{{ $coupons->total() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
                                <div class="card stat-card bg-gradient-success text-white border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="icon-circle text-white">
                                            <i class="fas fa-check-circle fa-fw"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">كوبونات نشطة</h6>
                                            <h3 class="mb-0">{{ $coupons->where('is_active', 1)->count() }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="card stat-card bg-gradient-info text-white border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="icon-circle text-white">
                                            <i class="fas fa-shopping-cart fa-fw"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">إجمالي الاستخدامات</h6>
                                            <h3 class="mb-0">{{ $coupons->sum('used_count') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions & Search -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-tags text-primary me-2"></i>
                                                قائمة كوبونات الخصم
                                            </h5>
                                        </div>
                                        <div class="actions">
                                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus-circle me-1"></i>
                                                إضافة كوبون جديد
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alerts -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Coupons List -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>الكود</th>
                                                        <th>الاسم</th>
                                                        <th>القيمة</th>
                                                        <th>الحالة</th>
                                                        <th>النوع</th>
                                                        <th>تاريخ الانتهاء</th>
                                                        <th>الاستخدام</th>
                                                        <th>الإجراءات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($coupons as $coupon)
                                                        <tr>
                                                            <td>
                                                                <span class="coupon-code" title="اضغط للنسخ" data-code="{{ $coupon->code }}">
                                                                    {{ $coupon->code }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('admin.coupons.show', $coupon) }}" class="text-decoration-none fw-medium text-dark">
                                                                    {{ $coupon->name }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                @if ($coupon->type === 'percentage')
                                                                    <span class="badge bg-primary-subtle text-primary">{{ $coupon->value }}%</span>
                                                                @else
                                                                    <span class="badge bg-info-subtle text-info">{{ number_format($coupon->value, 2) }} ر.س</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($coupon->is_active)
                                                                    @if ($coupon->expires_at && $coupon->expires_at->isPast())
                                                                        <span class="badge bg-danger">منتهي</span>
                                                                    @else
                                                                        <span class="badge bg-success">نشط</span>
                                                                    @endif
                                                                @else
                                                                    <span class="badge bg-secondary">غير نشط</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="d-flex flex-column gap-1">
                                                                    @if($coupon->applies_to_products)
                                                                        <span class="badge bg-primary-subtle text-primary">
                                                                            <i class="fas fa-shopping-bag me-1"></i>
                                                                            منتجات
                                                                        </span>
                                                                    @endif
                                                                    @if($coupon->applies_to_packages)
                                                                        <span class="badge bg-info-subtle text-info">
                                                                            <i class="fas fa-box me-1"></i>
                                                                            استوديو
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @if ($coupon->expires_at)
                                                                    @if ($coupon->expires_at->isPast())
                                                                        <span class="badge bg-danger">منتهي ({{ $coupon->expires_at->format('Y-m-d') }})</span>
                                                                    @else
                                                                        <div class="d-flex align-items-center">
                                                                            <span class="me-1">{{ $coupon->expires_at->format('Y-m-d') }}</span>
                                                                            <small class="text-muted">({{ $coupon->expires_at->diffForHumans() }})</small>
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <span class="badge bg-light text-secondary">غير محدد</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="coupon-usage">
                                                                    <div class="d-flex align-items-center">
                                                                        <strong>{{ $coupon->used_count }}</strong>
                                                                        @if ($coupon->max_uses)
                                                                            <span class="text-muted ms-1">/ {{ $coupon->max_uses }}</span>
                                                                            @php
                                                                                $percentage = ($coupon->max_uses > 0) ? min(100, round(($coupon->used_count / $coupon->max_uses) * 100)) : 0;
                                                                                $progressClass = $percentage < 50 ? 'bg-success' : ($percentage < 80 ? 'bg-warning' : 'bg-danger');
                                                                            @endphp
                                                                            <div class="progress ms-2" style="width: 40px; height: 5px;">
                                                                                <div class="progress-bar {{ $progressClass }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="action-buttons">
                                                                    <a href="{{ route('admin.coupons.show', $coupon) }}" class="btn btn-light-info" title="عرض">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-light-primary" title="تعديل">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline-block">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="btn btn-light-danger" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا الكوبون؟')">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center py-4 text-muted">
                                                                <div class="empty-state text-center py-3">
                                                                    <div class="empty-icon">
                                                                        <i class="fas fa-tags fa-2x text-muted"></i>
                                                                    </div>
                                                                    <p>لا توجد كوبونات خصم حالياً</p>
                                                                    <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm mt-2">
                                                                        <i class="fas fa-plus-circle me-1"></i>
                                                                        إضافة كوبون
                                                                    </a>
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

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $coupons->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تفعيل ميزة نسخ كود الكوبون عند النقر عليه
        const couponCodes = document.querySelectorAll('.coupon-code');

        couponCodes.forEach(code => {
            code.addEventListener('click', function() {
                const couponCode = this.getAttribute('data-code');
                navigator.clipboard.writeText(couponCode)
                    .then(() => {
                        // إنشاء عنصر موجز لإظهار رسالة النجاح
                        const toast = document.createElement('div');
                        toast.className = 'toast-notification';
                        toast.innerHTML = `
                            <div class="toast-content">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                تم نسخ الكود: ${couponCode}
                            </div>
                        `;
                        document.body.appendChild(toast);

                        // جعل عنصر الكود ينبض مرة واحدة
                        this.classList.add('copied');

                        // إزالة عنصر الموجز والصنف بعد فترة قصيرة
                        setTimeout(() => {
                            toast.classList.add('hide');
                            setTimeout(() => {
                                document.body.removeChild(toast);
                            }, 300);
                        }, 2000);

                        setTimeout(() => {
                            this.classList.remove('copied');
                        }, 1000);
                    })
                    .catch(err => {
                        console.error('فشل في نسخ النص: ', err);
                    });
            });
        });
    });
</script>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/coupon.css') }}?t={{ time() }}">
<style>
    /* أنماط عنصر الموجز */
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 0;
        transition: all 0.3s ease;
        transform: translateY(0);
        opacity: 1;
    }

    .toast-notification.hide {
        transform: translateY(-20px);
        opacity: 0;
    }

    .toast-content {
        padding: 12px 16px;
        display: flex;
        align-items: center;
    }

    /* أنيميشن النسخ */
    .coupon-code.copied {
        animation: pulse 0.5s;
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }
</style>
@endsection
