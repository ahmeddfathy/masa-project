@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-header-title">إدارة الحجوزات</h1>
                <div class="page-header-subtitle">عرض وإدارة جميع حجوزات الاستوديو</div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.bookings.calendar') }}" class="btn btn-primary">
                    <i class="fas fa-calendar-alt ml-1"></i>
                    عرض التقويم
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.bookings.index') }}" method="GET" id="searchForm">
                <div class="row g-3">
                    <!-- البحث برقم الحجز -->
                    <div class="col-md-3">
                        <label class="form-label">البحث برقم الحجز</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="booking_number"
                                placeholder="ادخل رقم الحجز" value="{{ $search_booking_number ?? '' }}">
                            @if(!empty($search_booking_number))
                            <button class="btn btn-outline-secondary clear-search" type="button" data-clear="booking_number">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- تصفية حسب الحالة -->
                    <div class="col-md-3">
                        <label class="form-label">تصفية حسب الحالة</label>
                        <select class="form-select" id="status-filter" name="status">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ ($search_status ?? '') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="confirmed" {{ ($search_status ?? '') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                            <option value="completed" {{ ($search_status ?? '') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ ($search_status ?? '') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>

                    <!-- تصفية حسب التاريخ -->
                    <div class="col-md-3">
                        <label class="form-label">تصفية حسب التاريخ</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="date-filter" name="date" value="{{ $search_date ?? '' }}">
                            @if(!empty($search_date))
                            <button class="btn btn-outline-secondary clear-search" type="button" data-clear="date">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- أزرار البحث والإلغاء -->
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> بحث
                            </button>
                            @if(!empty($search_booking_number) || !empty($search_status) || !empty($search_date))
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> إلغاء البحث
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                الحجوزات
                @if(!empty($search_booking_number) || !empty($search_status) || !empty($search_date))
                    <span class="badge bg-info">{{ $bookings->total() }} نتيجة</span>
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">العميل</th>
                                <th scope="col">نوع الجلسة</th>
                                <th scope="col">الباقة</th>
                                <th scope="col">التاريخ</th>
                                <th scope="col">الوقت</th>
                                <th scope="col">المبلغ</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>{{ $booking->booking_number }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-initial rounded-circle bg-primary">
                                                {{ substr($booking->user->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $booking->user->name }}</span>
                                            <small class="text-muted">{{ $booking->user->phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $booking->service->name }}</td>
                                <td>{{ $booking->package->name }}</td>
                                <td>{{ $booking->session_date->format('Y-m-d') }}</td>
                                <td>{{ $booking->session_time->format('H:i') }}</td>
                                <td>
                                    <span class="fw-bold">{{ $booking->total_amount }}</span>
                                    <small class="text-muted">درهم</small>
                                </td>
                                <td>
                                    <form action="{{ route('admin.bookings.update-status', $booking->uuid) }}"
                                          method="POST"
                                          class="d-flex gap-2 align-items-center status-form">
                                        @csrf
                                        @method('PATCH')
                                        <select class="form-select form-select-sm status-select" name="status">
                                            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>
                                                قيد الانتظار
                                            </option>
                                            <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>
                                                مؤكد
                                            </option>
                                            <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>
                                                مكتمل
                                            </option>
                                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>
                                                ملغي
                                            </option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.bookings.show', $booking->uuid) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger"
                                                onclick="confirmDelete('{{ $booking->uuid }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times empty-state-icon"></i>
                                        <h4>لا توجد حجوزات</h4>
                                        <p class="text-muted">لم يتم العثور على أي حجوزات تطابق معايير البحث</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if($bookings->hasPages())
        <div class="card-footer border-0 py-3">
            <nav aria-label="صفحات الحجوزات">
                <ul class="pagination mb-0">
                    {{-- Previous Page Link --}}
                    @if ($bookings->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" aria-hidden="true">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $bookings->previousPageUrl() }}" rel="prev">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                        @if ($page == $bookings->currentPage())
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
                    @if ($bookings->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $bookings->nextPageUrl() }}" rel="next">
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

@push('scripts')
<script>
// تحديث التنسيقات للحالة المحددة
document.querySelectorAll('.status-select').forEach(select => {
    updateSelectStyle(select);

    select.addEventListener('change', function() {
        updateSelectStyle(this);
    });
});

function updateSelectStyle(select) {
    const status = select.value;
    const colors = {
        pending: '#ffc107',    // warning
        confirmed: '#28a745',  // success
        completed: '#17a2b8',  // info
        cancelled: '#dc3545'   // danger
    };

    select.style.backgroundColor = colors[status] || '#6c757d';
    select.style.color = '#fff';
}

function confirmDelete(bookingId) {
    if (confirm('هل أنت متأكد من حذف هذا الحجز؟')) {
        // إرسال طلب الحذف
    }
}

// تفعيل الفلاتر
document.getElementById('status-filter').addEventListener('change', filterBookings);
document.getElementById('date-filter').addEventListener('change', filterBookings);

function filterBookings() {
    const status = document.getElementById('status-filter').value;
    const date = document.getElementById('date-filter').value;
    window.location.href = `{{ route('admin.bookings.index') }}?status=${status}&date=${date}`;
}

// إضافة معالجات الأحداث لأزرار مسح البحث
document.querySelectorAll('.clear-search').forEach(button => {
    button.addEventListener('click', function() {
        const fieldName = this.getAttribute('data-clear');
        const input = document.querySelector(`[name="${fieldName}"]`);
        if (input) {
            input.value = '';
            document.getElementById('searchForm').submit();
        }
    });
});
</script>
@endpush

@section('styles')
<link rel="stylesheet" href="/assets/css/admin/bookings.css">
@endsection
@endsection
