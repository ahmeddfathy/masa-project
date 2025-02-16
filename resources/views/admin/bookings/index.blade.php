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
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">تصفية حسب الحالة</label>
                    <select class="form-select" id="status-filter">
                        <option value="">جميع الحالات</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="confirmed">مؤكد</option>
                        <option value="completed">مكتمل</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">تصفية حسب التاريخ</label>
                    <input type="date" class="form-control" id="date-filter">
                </div>
                <div class="col-md-3">
                    <label class="form-label">بحث</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="ابحث عن عميل...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Table Card -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="thead-light">
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
                            <td>{{ $booking->id }}</td>
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
                                <select class="form-select form-select-sm status-select w-auto"
                                        data-booking-id="{{ $booking->id }}"
                                        onchange="updateStatus(this, {{ $booking->id }})">
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
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete({{ $booking->id }})">
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
        <div class="card-footer border-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatus(select, bookingId) {
    const status = select.value;
    fetch(`/admin/bookings/${bookingId}/update-status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث لون الخلفية حسب الحالة
            select.classList.remove('bg-warning', 'bg-success', 'bg-info', 'bg-danger');
            select.classList.add(`bg-${getStatusColor(status)}`);
        }
    });
}

function getStatusColor(status) {
    const colors = {
        pending: 'warning',
        confirmed: 'success',
        completed: 'info',
        cancelled: 'danger'
    };
    return colors[status] || 'secondary';
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
</script>
@endpush

@section('styles')
<style>
/* Avatar Styles */
.avatar {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
}

/* Status Select Styles */
.status-select {
    min-width: 140px;
}

/* Page Header Styles */
.page-header {
    padding: 1.5rem 0;
}

.page-header-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
}

.page-header-subtitle {
    color: #6c757d;
    font-size: 1rem;
}
</style>
@endsection
@endsection
