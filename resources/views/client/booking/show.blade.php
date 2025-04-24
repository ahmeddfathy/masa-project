@extends('layouts.customer')

@section('title', 'تفاصيل الحجز')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/booking/show.css') }}?t={{ time() }}">

@endsection

@section('content')
<header class="header-container">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="page-title">تفاصيل الحجز #{{ $booking->booking_number }}</h2>
                <p class="page-subtitle">{{ $booking->created_at->format('Y/m/d') }}</p>
            </div>
            <div class="col-md-6 text-start">
                <a href="{{ route('client.bookings.my') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-right"></i>
                    العودة للحجوزات
                </a>
            </div>
        </div>
    </div>
</header>

<div class="container py-4">
    <div class="booking-details">
        <!-- Booking Header -->
        <div class="booking-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2">تفاصيل الحجز #{{ $booking->booking_number }}</h2>
                <p class="text-light mb-0">
                    <i class="fas fa-calendar-alt me-1"></i>
                    تاريخ الحجز: {{ $booking->created_at->format('Y/m/d') }}
                </p>
            </div>
            <div class="text-end">
                @switch($booking->status)
                    @case('pending')
                        <span class="badge bg-warning">
                            <i class="fas fa-clock me-1"></i>
                            في انتظار التأكيد
                        </span>
                        @break
                    @case('confirmed')
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>
                            تم تأكيد الحجز
                        </span>
                        @break
                    @case('completed')
                        <span class="badge bg-info">
                            <i class="fas fa-check-double me-1"></i>
                            تم إكمال الجلسة
                        </span>
                        @break
                    @case('cancelled')
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle me-1"></i>
                            تم إلغاء الحجز
                        </span>
                        @break
                    @case('payment_required')
                        <span class="badge bg-warning">
                            <i class="fas fa-credit-card me-1"></i>
                            بانتظار الدفع
                        </span>
                        @break
                    @case('payment_failed')
                        <span class="badge bg-danger">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            فشل الدفع
                        </span>
                        @break
                    @default
                        <span class="badge bg-secondary">
                            <i class="fas fa-question-circle me-1"></i>
                            غير معروف
                        </span>
                @endswitch
            </div>
        </div>

        <!-- Status Message -->
        @switch($booking->status)
            @case('pending')
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    حجزك في انتظار المراجعة والتأكيد من قبل الاستوديو. سيتم التواصل معك قريباً.
                </div>
                @break
            @case('confirmed')
                <div class="alert alert-success mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    تم تأكيد حجزك. نتطلع لرؤيتك في الموعد المحدد.
                </div>
                @break
            @case('completed')
                <div class="alert alert-info mb-4">
                    <i class="fas fa-check-double me-2"></i>
                    تم إكمال الجلسة بنجاح. شكراً لاختيارك عدسة سوما.
                </div>
                @break
            @case('cancelled')
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-times-circle me-2"></i>
                    تم إلغاء هذا الحجز.
                    @if($booking->cancellation_reason)
                        <br>
                        <small>السبب: {{ $booking->cancellation_reason }}</small>
                    @endif
                </div>
                @break
            @case('payment_required')
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-credit-card me-2"></i>
                    يرجى إكمال عملية الدفع لتأكيد الحجز.
                    <div class="mt-3">
                        <form action="{{ route('client.bookings.retry-payment', $booking->uuid) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-credit-card me-1"></i>
                                الدفع الآن
                            </button>
                        </form>
                    </div>
                </div>
                @break
            @case('payment_failed')
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    فشلت عملية الدفع. يرجى المحاولة مرة أخرى.
                    <div class="mt-3">
                        <form action="{{ route('client.bookings.retry-payment', $booking->uuid) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-credit-card me-1"></i>
                                محاولة الدفع مرة أخرى
                            </button>
                        </form>
                    </div>
                </div>
                @break
        @endswitch

        <div class="booking-body">
            <div class="row">
                <!-- Session Details -->
                <div class="col-md-6">
                    <div class="info-group">
                        <h6><i class="fas fa-camera me-1"></i> الخدمة</h6>
                        <p class="mb-0">{{ $booking->service->name }}</p>
                    </div>
                    <div class="info-group">
                        <h6><i class="fas fa-box me-1"></i> الباقة</h6>
                        <p class="mb-0">{{ $booking->package->name }}</p>
                    </div>
                    <div class="info-group">
                        <h6><i class="fas fa-calendar me-1"></i> موعد الجلسة</h6>
                        <p class="mb-0">{{ $booking->session_date->format('Y/m/d') }} - {{ $booking->session_time->format('H:i A') }}</p>
                    </div>
                    <div class="info-group">
                        <h6><i class="fas fa-clock me-1"></i> مدة الجلسة</h6>
                        <p class="mb-0">{{ $booking->package->duration }} دقيقه</p>
                    </div>
                    <div class="info-group">
                        <h6><i class="fas fa-images me-1"></i> عدد الصور</h6>
                        <p class="mb-0">{{ $booking->package->num_photos }} صورة</p>
                    </div>
                    <div class="info-group">
                        <h6><i class="fas fa-palette me-1"></i> عدد الثيمات</h6>
                        <p class="mb-0">{{ $booking->package->themes_count }} ثيم</p>
                    </div>
                </div>

                <!-- Baby Details -->
                <div class="col-md-6">
                    @if($booking->baby_name)
                    <div class="info-group">
                        <h6><i class="fas fa-baby me-1"></i> اسم المولود</h6>
                        <p class="mb-0">{{ $booking->baby_name }}</p>
                    </div>
                    @endif
                    @if($booking->baby_birth_date)
                    <div class="info-group">
                        <h6><i class="fas fa-birthday-cake me-1"></i> تاريخ الميلاد</h6>
                        <p class="mb-0">{{ $booking->baby_birth_date->format('Y/m/d') }}</p>
                    </div>
                    @endif
                    @if($booking->gender)
                    <div class="info-group">
                        <h6><i class="fas fa-venus-mars me-1"></i> الجنس</h6>
                        <p class="mb-0">{{ $booking->gender }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Addons -->
            @if($booking->addons->count() > 0)
            <div class="mt-4">
                <h5 class="text-primary mb-3">الإضافات المختارة</h5>
                @foreach($booking->addons as $addon)
                <div class="addon-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ $addon->name }}</h6>
                            <p class="text-muted mb-0">{{ $addon->description }}</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-0">{{ $addon->pivot->quantity }} × {{ $addon->pivot->price_at_booking }} ريال سعودي</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Payment Information -->
            <div class="mt-4">
                <h5 class="text-primary mb-3">معلومات الدفع</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group">
                            <h6><i class="fas fa-money-bill me-1"></i> المبلغ الإجمالي</h6>
                            <p class="mb-0">{{ $booking->total_amount }} ريال سعودي</p>
                        </div>
                        <div class="info-group">
                            <h6><i class="fas fa-receipt me-1"></i> حالة الدفع</h6>
                            <p class="mb-0">
                                @if($booking->status === 'confirmed')
                                    <span class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        تم الدفع
                                    </span>
                                @elseif($booking->status === 'pending')
                                    <span class="text-warning">
                                        <i class="fas fa-clock me-1"></i>
                                        قيد المعالجة
                                    </span>
                                @elseif(in_array($booking->status, ['payment_required', 'payment_failed']))
                                    <span class="text-danger">
                                        <i class="fas fa-times-circle me-1"></i>
                                        لم يتم الدفع
                                    </span>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-question-circle me-1"></i>
                                        غير متوفر
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if($booking->payment_id)
                        <div class="info-group">
                            <h6><i class="fas fa-hashtag me-1"></i> رقم العملية</h6>
                            <p class="mb-0">{{ $booking->payment_id }}</p>
                        </div>
                        @endif
                        @if($booking->transaction_reference)
                        <div class="info-group">
                            <h6><i class="fas fa-file-invoice me-1"></i> رقم المرجع</h6>
                            <p class="mb-0">{{ $booking->transaction_reference }}</p>
                        </div>
                        @endif
                        @if($booking->payment_date)
                        <div class="info-group">
                            <h6><i class="fas fa-calendar-check me-1"></i> تاريخ الدفع</h6>
                            <p class="mb-0">{{ $booking->payment_date->format('Y/m/d H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($booking->notes)
            <div class="mt-4">
                <h5>ملاحظات إضافية</h5>
                <p class="mb-0">{{ $booking->notes }}</p>
            </div>
            @endif

            <!-- Consent Information -->
            <div class="mt-4">
                <h5>الموافقات</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group mb-md-0">
                            <h6><i class="fas fa-camera me-1"></i> الموافقة على عرض الصور</h6>
                            <p class="mb-0">
                                @if($booking->image_consent)
                                    <span class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        تمت الموافقة على عرض الصور
                                    </span>
                                @else
                                    <span class="text-danger">
                                        <i class="fas fa-times-circle me-1"></i>
                                        لم تتم الموافقة على عرض الصور
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="actions mt-4">
                <a href="{{ route('client.bookings.my') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-right me-1"></i>
                    العودة للحجوزات
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
