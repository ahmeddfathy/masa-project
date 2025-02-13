@extends('layouts.customer')

@section('title', 'تفاصيل الحجز')

@section('styles')
<style>
    .booking-details {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .booking-header {
        border-bottom: 1px solid #eee;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
    }

    .booking-status {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-group h6 {
        color: #666;
        margin-bottom: 0.5rem;
    }

    .info-group p {
        color: #333;
        font-weight: 500;
        margin-bottom: 0;
    }

    .addon-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .price-details {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .total-price {
        border-top: 2px solid #eee;
        margin-top: 1rem;
        padding-top: 1rem;
        font-weight: bold;
    }

    .actions {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="booking-details">
        <!-- Booking Header -->
        <div class="booking-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2">تفاصيل الحجز #{{ $booking->id }}</h2>
                <p class="text-muted mb-0">
                    <i class="fas fa-calendar-alt me-1"></i>
                    تاريخ الحجز: {{ $booking->created_at->format('Y/m/d') }}
                </p>
            </div>
            <span class="booking-status badge bg-{{ $booking->status_color }}">
                {{ $booking->status_text }}
            </span>
        </div>

        <div class="row">
            <!-- Session Details -->
            <div class="col-md-6">
                <div class="info-group">
                    <h6><i class="fas fa-camera me-1"></i> الخدمة</h6>
                    <p>{{ $booking->service->name }}</p>
                </div>
                <div class="info-group">
                    <h6><i class="fas fa-box me-1"></i> الباقة</h6>
                    <p>{{ $booking->package->name }}</p>
                </div>
                <div class="info-group">
                    <h6><i class="fas fa-calendar me-1"></i> موعد الجلسة</h6>
                    <p>{{ $booking->session_date->format('Y/m/d') }} - {{ $booking->session_time->format('H:i A') }}</p>
                </div>
            </div>

            <!-- Baby Details -->
            <div class="col-md-6">
                @if($booking->baby_name)
                <div class="info-group">
                    <h6><i class="fas fa-baby me-1"></i> اسم المولود</h6>
                    <p>{{ $booking->baby_name }}</p>
                </div>
                @endif
                @if($booking->baby_birth_date)
                <div class="info-group">
                    <h6><i class="fas fa-birthday-cake me-1"></i> تاريخ الميلاد</h6>
                    <p>{{ $booking->baby_birth_date->format('Y/m/d') }}</p>
                </div>
                @endif
                @if($booking->gender)
                <div class="info-group">
                    <h6><i class="fas fa-venus-mars me-1"></i> الجنس</h6>
                    <p>{{ $booking->gender }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Addons -->
        @if($booking->addons->count() > 0)
        <div class="mt-4">
            <h5 class="mb-3">الإضافات المختارة</h5>
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
            <h5 class="mb-3">تفاصيل السعر</h5>
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
            <div class="price-row total-price">
                <span>الإجمالي</span>
                <span>{{ $booking->total_amount }} درهم</span>
            </div>
        </div>

        <!-- Notes -->
        @if($booking->notes)
        <div class="mt-4">
            <h5 class="mb-3">ملاحظات إضافية</h5>
            <p class="mb-0">{{ $booking->notes }}</p>
        </div>
        @endif

        <!-- Actions -->
        <div class="actions">
            <a href="{{ route('client.bookings.my') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                العودة للحجوزات
            </a>
        </div>
    </div>
</div>
@endsection
