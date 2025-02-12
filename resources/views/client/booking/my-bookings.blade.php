<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجوزاتي - Lense Soma Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/booking/my-bookings.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/images/logo.png" alt="Lense Soma Studio">
            </a>
        </div>
    </nav>

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
                                <h5 class="mb-0">حجز #{{ $booking->id }}</h5>
                                <span class="status-badge status-{{ $booking->status }}">
                                    @switch($booking->status)
                                        @case('pending')
                                            قيد الانتظار
                                            @break
                                        @case('confirmed')
                                            مؤكد
                                            @break
                                        @case('completed')
                                            مكتمل
                                            @break
                                        @case('cancelled')
                                            ملغي
                                            @break
                                    @endswitch
                                </span>
                            </div>
                            <div class="booking-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>نوع الجلسة:</strong> {{ $booking->service->name }}</p>
                                        <p><strong>الباقة:</strong> {{ $booking->package->name }}</p>
                                        <p><strong>تاريخ الجلسة:</strong> {{ $booking->session_date->format('Y-m-d') }}</p>
                                        <p><strong>وقت الجلسة:</strong> {{ $booking->session_time->format('H:i') }}</p>
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
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
