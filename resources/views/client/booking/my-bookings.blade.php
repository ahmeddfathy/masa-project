@extends('layouts.customer')

@section('title', 'حجوزاتي')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/booking/my-bookings.css') }}?t={{ time() }}">
@endsection

@section('content')
<header class="header-container">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="page-title">حجوزاتي</h2>
                <p class="page-subtitle">إدارة وتتبع حجوزات جلسات التصوير</p>
            </div>
            <div class="col-md-6 text-start">
                <a href="{{ route('client.bookings.create') }}" class="btn btn-outline-primary">
                    <i class="bi bi-calendar-plus"></i>
                    حجز جديد
                </a>
            </div>
        </div>
    </div>
</header>

<main class="container py-4">
    @forelse($bookings as $booking)
    <div class="booking-card">
        <div class="booking-header">
            <div class="d-flex align-items-center">
                <div class="booking-icon">
                    <i class="bi bi-camera"></i>
                </div>
                <div class="ms-3">
                    <h3 class="booking-number">حجز #{{ $booking->booking_number }}</h3>
                    <span class="status-badge status-{{ $booking->status }}">
                        {{ match($booking->status) {
                            'confirmed' => 'مؤكد',
                            'completed' => 'مكتمل',
                            'cancelled' => 'ملغي',
                            'processing' => 'قيد المعالجة',
                            'pending' => 'قيد الانتظار',
                            'payment_required' => 'بانتظار الدفع',
                            'payment_failed' => 'فشل الدفع',
                            default => 'غير معروف'
                        } }}
                    </span>
                </div>
            </div>
            <div class="booking-meta">
                <div class="booking-date">
                    <i class="bi bi-calendar"></i>
                    {{ $booking->session_date->format('Y/m/d') }}
                </div>
                <div class="booking-time">
                    <i class="bi bi-clock"></i>
                    {{ $booking->session_time->format('H:i') }}
                </div>
                <div class="booking-total">
                    {{ $booking->total_amount }} ريال سعودي
                </div>
                @if(in_array($booking->status, ['pending', 'payment_failed', 'payment_required']))
                <form action="{{ route('client.bookings.retry-payment', $booking->uuid) }}" method="POST" class="ms-2">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-credit-card"></i>
                        إتمام الدفع
                    </button>
                </form>
                @endif
                <a href="{{ route('client.bookings.show', $booking->uuid) }}" class="btn btn-primary">
                    <i class="bi bi-eye"></i>
                    عرض التفاصيل
                </a>
            </div>
        </div>

        <div class="booking-details">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-group">
                        <h6><i class="bi bi-camera me-1"></i> الخدمة</h6>
                        <p>{{ $booking->service->name }}</p>
                    </div>
                    <div class="info-group">
                        <h6><i class="bi bi-box me-1"></i> الباقة</h6>
                        <p>{{ $booking->package->name }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    @if($booking->baby_name)
                    <div class="info-group">
                        <h6><i class="bi bi-person me-1"></i> اسم المولود</h6>
                        <p>{{ $booking->baby_name }}</p>
                    </div>
                    @endif
                    <div class="info-group">
                        <h6><i class="bi bi-clock-history me-1"></i> مدة الجلسة</h6>
                        <p>{{ $booking->package->duration }} دقيقه</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="bi bi-calendar"></i>
        </div>
        <h3>لا توجد حجوزات حتى الآن</h3>
        <p>قم بحجز جلسة تصوير جديدة واستمتع بتجربة تصوير مميزة</p>
        <a href="{{ route('client.bookings.create') }}" class="btn btn-primary">
            حجز جلسة جديدة
        </a>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($bookings->hasPages())
    <nav aria-label="Page navigation">
        <div class="pagination">
            {{-- Previous Page Link --}}
            @if($bookings->onFirstPage())
                <span class="page-item disabled">
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </span>
            @else
                <span class="page-item">
                    <a class="page-link" href="{{ $bookings->previousPageUrl() }}" rel="prev">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </span>
            @endif

            {{-- Pagination Elements --}}
            @foreach($bookings->getUrlRange(1, $bookings->lastPage()) as $page => $url)
                @if($page == $bookings->currentPage())
                    <span class="page-item active">
                        <span class="page-link">{{ $page }}</span>
                    </span>
                @else
                    <span class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </span>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if($bookings->hasMorePages())
                <span class="page-item">
                    <a class="page-link" href="{{ $bookings->nextPageUrl() }}" rel="next">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </span>
            @else
                <span class="page-item disabled">
                    <span class="page-link" aria-hidden="true">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </span>
            @endif
        </div>
    </nav>
    @endif
</main>
@endsection
