@extends('layouts.customer')

@section('title', 'تفاصيل الحجز - Lense Soma Studio')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/booking/show.css') }}">
@endsection

@section('content')
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
                <span class="booking-status badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                    {{ $booking->status === 'confirmed' ? 'تم الدفع' : ($booking->status === 'pending' ? 'قيد المعالجة' : 'فشل الدفع') }}
                </span>
            </div>
        </div>

        <div class="booking-body">
            @if($booking->status === 'pending')
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-clock me-2"></i>
                    الحجز قيد المعالجة. سيتم تحديث حالة الحجز تلقائياً عند اكتمال عملية الدفع.
                </div>
            @elseif($booking->status !== 'confirmed')
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    فشلت عملية الدفع. يرجى المحاولة مرة أخرى أو التواصل مع الدعم الفني.
                </div>
            @endif
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
                        <p class="mb-0">{{ $booking->package->duration }} ساعة</p>
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
                            <p class="mb-0">{{ $addon->pivot->quantity }} × {{ $addon->pivot->price_at_booking }} درهم</p>
                            <p class="mb-0 text-primary">
                                الإجمالي: {{ $addon->pivot->quantity * $addon->pivot->price_at_booking }} درهم
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Price Details -->
            <div class="price-details">
                <h5>تفاصيل السعر</h5>
                <div class="price-row">
                    <span>سعر الباقة الأساسي</span>
                    <span>{{ $booking->package->base_price }} درهم</span>
                </div>
                @if($booking->addons->count() > 0)
                <div class="price-row">
                    <span>الإضافات</span>
                    <span>{{ $booking->addons->sum(function($addon) {
                        return $addon->pivot->quantity * $addon->pivot->price_at_booking;
                    }) }} درهم</span>
                </div>
                @endif
                <div class="total-price d-flex justify-content-between">
                    <span>الإجمالي</span>
                    <span>{{ $booking->total_amount }} درهم</span>
                </div>
            </div>

            <!-- Notes -->
            @if($booking->notes)
            <div class="consent-section">
                <h5>ملاحظات إضافية</h5>
                <p class="mb-0">{{ $booking->notes }}</p>
            </div>
            @endif

            <!-- Consent Information -->
            <div class="consent-section">
                <h5>الموافقات</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-group mb-md-0">
                            <h6><i class="fas fa-camera me-1"></i> الموافقة على عرض الصور</h6>
                            <p class="mb-0">
                                @if($booking->image_consent)
                                    <span class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        تمت الموافقة على عرض الصور في معرض الاستوديو ومواقع التواصل الاجتماعي
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
                    <div class="col-md-6">
                        <div class="info-group mb-0">
                            <h6><i class="fas fa-file-contract me-1"></i> الموافقة على الشروط والسياسات</h6>
                            <p class="mb-0">
                                <span class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    تمت الموافقة على الشروط والسياسات
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="actions">
                <a href="{{ route('client.bookings.my') }}" class="btn-back">
                    <i class="fas fa-arrow-right me-1"></i>
                    العودة للحجوزات
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
