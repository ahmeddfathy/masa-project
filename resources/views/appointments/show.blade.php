@extends('layouts.customer')

@section('title', 'تفاصيل الموعد')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('assets/css/customer/appointments.css') }}">
<style>
    .custom-design-badge {
        background-color: #6c5ce7;
        color: white;
        padding: 0.35em 0.65em;
        font-size: 0.85em;
        font-weight: 600;
        border-radius: 0.25rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .custom-design-badge i {
        font-size: 1.1em;
    }

    .appointment-card.custom-design {
        border-top: 4px solid #6c5ce7;
    }

    .appointment-card.custom-design .info-icon {
        color: #6c5ce7;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title mb-0">تفاصيل الموعد</h2>
        <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-right"></i>
            العودة للمواعيد
        </a>
    </div>

    <div class="appointment-container">
        <div class="appointment-card {{ $appointment->service_type === 'custom_design' ? 'custom-design' : '' }}">
            <div class="appointment-header">
                <div class="d-flex align-items-center gap-3">
                    <h4 class="mb-0">
                        <span class="text-muted">رقم الموعد:</span>
                        {{ $appointment->reference_number }}
                    </h4>
                    <button class="btn btn-sm btn-outline-primary"
                            onclick="navigator.clipboard.writeText('{{ $appointment->reference_number }}')">
                        <i class="bi bi-clipboard"></i>
                        نسخ
                    </button>
                </div>
                <span class="appointment-status status-{{ $appointment->status }}">
                    {{ $appointment->status_text }}
                </span>
            </div>

            <div class="info-section">
                <h5 class="section-title">
                    <i class="bi bi-info-circle me-2"></i>
                    معلومات الموعد
                </h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-grid"></i>
                            </div>
                            <div>
                                <div class="info-label">نوع الخدمة</div>
                                <div class="info-value">
                                    @if($appointment->service_type === 'custom_design')
                                        <span class="custom-design-badge">
                                            <i class="bi bi-brush"></i>
                                            تصميم مخصص
                                        </span>
                                    @else
                                        {{ $appointment->service }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-calendar"></i>
                            </div>
                            <div>
                                <div class="info-label">تاريخ الموعد</div>
                                <div class="info-value">{{ $appointment->appointment_date->format('Y/m/d') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <div class="info-label">وقت الموعد</div>
                                <div class="info-value">{{ $appointment->appointment_time->format('h:i A') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <div class="info-label">الموقع</div>
                                <div class="info-value">{{ $appointment->location }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($appointment->notes)
            <div class="info-section">
                <h5 class="section-title">
                    <i class="bi bi-card-text me-2"></i>
                    ملاحظات
                </h5>
                <p class="mb-0">{{ $appointment->notes }}</p>
            </div>
            @endif

            <div class="info-section">
                <h5 class="section-title">
                    <i class="bi bi-clock-history me-2"></i>
                    سجل الموعد
                </h5>
                <div class="timeline">
                    @if(isset($appointment->history) && count($appointment->history) > 0)
                    @foreach($appointment->history as $history)
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-date">{{ $history->created_at->format('Y/m/d h:i A') }}</div>
                            <p class="timeline-text">{{ $history->description }}</p>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <p class="timeline-text text-muted">لا يوجد سجل للموعد حتى الآن</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
