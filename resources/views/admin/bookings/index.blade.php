@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إدارة الحجوزات</h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <select class="form-select" id="status-filter">
                                <option value="">كل الحالات</option>
                                <option value="pending">قيد الانتظار</option>
                                <option value="confirmed">مؤكد</option>
                                <option value="completed">مكتمل</option>
                                <option value="cancelled">ملغي</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="date-filter">
                        </div>
                    </div>

                    <!-- Bookings Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>رقم الحجز</th>
                                    <th>العميل</th>
                                    <th>نوع الجلسة</th>
                                    <th>الباقة</th>
                                    <th>تاريخ الجلسة</th>
                                    <th>الحالة</th>
                                    <th>المبلغ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bookings as $booking)
                                <tr>
                                    <td>#{{ $booking->id }}</td>
                                    <td>{{ $booking->user->name }}</td>
                                    <td>{{ $booking->service->name }}</td>
                                    <td>{{ $booking->package->name }}</td>
                                    <td>{{ $booking->session_date->format('Y-m-d') }}</td>
                                    <td>
                                        <form action="{{ route('admin.bookings.update-status', $booking) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select form-select-sm"
                                                    onchange="this.form.submit()">
                                                <option value="pending"
                                                    {{ $booking->status == 'pending' ? 'selected' : '' }}>
                                                    قيد الانتظار
                                                </option>
                                                <option value="confirmed"
                                                    {{ $booking->status == 'confirmed' ? 'selected' : '' }}>
                                                    مؤكد
                                                </option>
                                                <option value="completed"
                                                    {{ $booking->status == 'completed' ? 'selected' : '' }}>
                                                    مكتمل
                                                </option>
                                                <option value="cancelled"
                                                    {{ $booking->status == 'cancelled' ? 'selected' : '' }}>
                                                    ملغي
                                                </option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $booking->total_amount }} درهم</td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking) }}"
                                           class="btn btn-sm btn-info">
                                            عرض التفاصيل
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Filter functionality
    document.getElementById('status-filter').addEventListener('change', function() {
        filterBookings();
    });

    document.getElementById('date-filter').addEventListener('change', function() {
        filterBookings();
    });

    function filterBookings() {
        const status = document.getElementById('status-filter').value;
        const date = document.getElementById('date-filter').value;

        window.location.href = `{{ route('admin.bookings.index') }}?status=${status}&date=${date}`;
    }
</script>
@endpush
@endsection
