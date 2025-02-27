<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم الحجز بنجاح - Lense Soma Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/booking/success.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #21B3B0 !important;
            --secondary-color: #21B3B0 !important;
            --primary-gradient: linear-gradient(45deg, #21B3B0, #21B3B0) !important;
            --secondary-gradient: linear-gradient(45deg, #21B3B0, #21B3B0) !important;
        }

        .success-icon {
            border-color: #21B3B0 !important;
            color: #21B3B0 !important;
        }

        .success-icon::after {
            border-color: #21B3B0 !important;
        }

        .success-card h2 {
            color: #21B3B0 !important;
        }

        .booking-details h4 {
            color: #21B3B0 !important;
        }

        .booking-details h4::after {
            background: #21B3B0 !important;
        }

        .booking-details li strong {
            color: #21B3B0 !important;
        }

        .btn-primary {
            background: #21B3B0 !important;
        }

        .btn-outline-primary {
            border-color: #21B3B0 !important;
            color: #21B3B0 !important;
        }

        .btn-outline-primary:hover {
            background: #21B3B0 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="success-card">
                    <div class="success-icon">✓</div>
                    <h2 style="color: var(--primary-color)">تم الحجز بنجاح!</h2>
                    <p class="lead">شكراً لك على الحجز معنا. سنتواصل معك قريباً لتأكيد موعدك.</p>

                    <div class="booking-details">
                        <h4>تفاصيل الحجز:</h4>
                        <ul class="list-unstyled">
                            <li><strong>رقم الحجز:</strong> {{ $booking->booking_number }}</li>
                            <li><strong>حالة الدفع:</strong>
                                @if($booking->status === 'confirmed')
                                    <span class="badge bg-success">تم الدفع</span>
                                @elseif($booking->status === 'pending')
                                    <span class="badge bg-warning">قيد المعالجة</span>
                                @else
                                    <span class="badge bg-danger">فشل الدفع</span>
                                @endif
                            </li>
                            <li><strong>نوع الجلسة:</strong> {{ $booking->service->name }}</li>
                            <li><strong>الباقة:</strong> {{ $booking->package->name }}</li>
                            <li><strong>التاريخ:</strong> {{ $booking->session_date->format('Y-m-d') }}</li>
                            <li><strong>الوقت:</strong> {{ $booking->session_time->format('H:i') }}</li>

                            @if($booking->addons->count() > 0)
                                <li>
                                    <strong>الإضافات المختارة:</strong>
                                    <ul>
                                        @foreach($booking->addons as $addon)
                                            <li>{{ $addon->name }} - {{ $addon->pivot->price_at_booking }} درهم</li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endif

                            <li><strong>المبلغ الإجمالي:</strong> {{ $booking->total_amount }} درهم</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        @if($booking->status === 'confirmed')
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                تم تأكيد الحجز والدفع بنجاح. سنتواصل معك قريباً لتأكيد موعدك.
                            </div>
                        @elseif($booking->status === 'pending')
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i>
                                الحجز قيد المعالجة. سيتم تحديث حالة الحجز تلقائياً عند اكتمال عملية الدفع.
                            </div>
                        @else
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                فشلت عملية الدفع. يرجى المحاولة مرة أخرى أو التواصل مع الدعم الفني.
                            </div>
                        @endif
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('client.bookings.my') }}" class="btn btn-primary">عرض حجوزاتي</a>
                        <a href="/" class="btn btn-outline-primary">العودة للرئيسية</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
