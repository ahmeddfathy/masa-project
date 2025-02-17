<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجوزاتي - Lense Soma Studio</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/booking/my-bookings.css') }}">
</head>
<body>
    @include('parts.navbar')

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 style="color: var(--primary-color)">حجوزاتي</h2>
                    <a href="{{ route('client.bookings.create') }}" class="btn btn-primary">
                        حجز جديد
                    </a>
                </div>

                @if($bookings->isEmpty())
                    <div class="alert alert-info text-center">
                        لا يوجد لديك حجوزات سابقة.
                    </div>
                @else
                    @foreach($bookings as $booking)
                        <div class="booking-card">
                            <div class="booking-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">حجز #{{ $booking->id }}</h5>
                                    <small class="text-muted">{{ $booking->created_at->format('Y-m-d H:i') }}</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge me-2 bg-{{ $booking->payment_status === 'A' ? 'success' : ($booking->payment_status === 'H' || $booking->payment_status === 'V' ? 'warning' : 'danger') }}">
                                        {{ $booking->payment_status === 'A' ? 'تم الدفع' : ($booking->payment_status === 'H' ? 'في انتظار الدفع' : ($booking->payment_status === 'V' ? 'قيد التحقق' : 'فشل الدفع')) }}
                                    </span>
                                </div>
                            </div>
                            <div class="booking-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>نوع الجلسة:</strong> {{ $booking->service->name }}</p>
                                        <p><strong>الباقة:</strong> {{ $booking->package->name }}</p>
                                        <p><strong>تاريخ الجلسة:</strong> {{ $booking->session_date->format('Y-m-d') }}</p>
                                        <p><strong>وقت الجلسة:</strong> {{ $booking->session_time->format('H:i') }}</p>
                                        <p>
                                            <strong>تفاصيل الباقة:</strong>
                                            <span class="badge bg-info me-1">{{ $booking->package->duration }} ساعة</span>
                                            <span class="badge bg-info me-1">{{ $booking->package->num_photos }} صورة</span>
                                            <span class="badge bg-info">{{ $booking->package->themes_count }} ثيم</span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        @if($booking->baby_name)
                                            <p><strong>اسم المولود:</strong> {{ $booking->baby_name }}</p>
                                        @endif
                                        @if($booking->baby_birth_date)
                                            <p><strong>تاريخ الميلاد:</strong> {{ $booking->baby_birth_date->format('Y-m-d') }}</p>
                                        @endif
                                        @if($booking->gender)
                                            <p><strong>الجنس:</strong> {{ $booking->gender }}</p>
                                        @endif
                                        <p><strong>المبلغ الإجمالي:</strong> {{ $booking->total_amount }} درهم</p>
                                        <p>
                                            <strong>عرض الصور:</strong>
                                            @if($booking->image_consent)
                                                <span class="text-success">
                                                    <i class="fas fa-check-circle"></i>
                                                    موافق على العرض
                                                </span>
                                            @else
                                                <span class="text-danger">
                                                    <i class="fas fa-times-circle"></i>
                                                    غير موافق على العرض
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                @if($booking->notes)
                                    <div class="mt-3">
                                        <strong>ملاحظات:</strong>
                                        <p class="mb-0">{{ $booking->notes }}</p>
                                    </div>
                                @endif
                                @if($booking->addons->count() > 0)
                                    <div class="col-12 mt-3">
                                        <strong>الإضافات:</strong>
                                        <ul>
                                            @foreach($booking->addons as $addon)
                                                <li>{{ $addon->name }} - {{ $addon->pivot->price_at_booking }} درهم</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Show Details Button -->
                                <div class="mt-3 text-end">
                                    <a href="{{ route('client.bookings.show', $booking) }}" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i>
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        @if ($bookings->hasPages())
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($bookings->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link prev" aria-hidden="true">&laquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link prev" href="{{ $bookings->previousPageUrl() }}" rel="prev">&laquo;</a>
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
                                            <a class="page-link next" href="{{ $bookings->nextPageUrl() }}" rel="next">&raquo;</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link next" aria-hidden="true">&raquo;</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
