<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم الحجز بنجاح - Lense Soma Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #40B3A2;
            --secondary-color: #FF69B4;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8f9fa;
        }

        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 40px;
            margin-top: 50px;
            text-align: center;
        }

        .success-icon {
            color: var(--primary-color);
            font-size: 60px;
            margin-bottom: 20px;
        }

        .booking-details {
            margin-top: 30px;
            text-align: right;
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
                            <li><strong>رقم الحجز:</strong> #{{ $booking->id }}</li>
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
