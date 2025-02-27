@extends('layouts.customer')

@section('title', 'حجوزاتي - Lense Soma Studio')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/booking/my-bookings.css') }}">

@endsection

@section('content')
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
                                <h5 class="mb-0">حجز #{{ $booking->booking_number }}</h5>
                                <small class="text-muted">{{ $booking->created_at->format('Y-m-d H:i') }}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge me-2 bg-{{ $booking->status === 'completed' ? 'success' : ($booking->status === 'pending' ? 'warning' : ($booking->status === 'cancelled' ? 'danger' : 'info')) }}">
                                    {{ $booking->status === 'completed' ? 'مكتمل' : ($booking->status === 'pending' ? 'قيد الانتظار' : ($booking->status === 'cancelled' ? 'ملغي' : 'جديد')) }}
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
                                <a href="{{ route('client.bookings.show', $booking->uuid) }}" class="btn btn-primary">
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
@endsection
