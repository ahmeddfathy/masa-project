@extends('layouts.customer')

@section('title', 'حجز موعد تصميم مخصص')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('assets/css/customer/appointments.css') }}">
<style>
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
        border: 2px solid #6c5ce7;
    }

    .section-title {
        color: #333;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label {
        font-weight: 500;
        color: #555;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #6c5ce7;
        box-shadow: 0 0 0 0.25rem rgba(108, 92, 231, 0.1);
    }

    .btn-submit {
        background: #6c5ce7;
        color: white;
        padding: 1rem 2rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background: #5849c2;
        transform: translateY(-2px);
    }

    .error-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .design-info {
        background: #f8f9fe;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    #appointmentErrors {
        display: none;
        margin-bottom: 1rem;
    }

    .loading-spinner {
        display: none;
        margin-right: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title mb-0">حجز موعد تصميم مخصص</h2>
        <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-right"></i>
            العودة للمواعيد
        </a>
    </div>

    <div class="alert alert-danger" id="appointmentErrors"></div>

    <form id="appointmentForm" class="appointment-form" data-url="{{ route('appointments.store') }}">
        @csrf
        <input type="hidden" name="service_type" value="custom_design">

        <div class="design-info">
            <h5 class="mb-3">
                <i class="bi bi-info-circle me-2"></i>
                معلومات عن التصميم المخصص
            </h5>
            <p>نقدم لكِ خدمة تصميم العبايات حسب رغبتك وذوقك الخاص. يرجى تقديم أكبر قدر من التفاصيل لمساعدتنا في فهم متطلباتك بشكل أفضل.</p>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-calendar-fill"></i>
                موعد الزيارة
            </h3>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                مواعيد العمل:
                <ul class="mb-0">
                    <li>من السبت إلى الخميس: ١١ صباحاً إلى ٢ ظهراً، و٥ عصراً إلى ١١ مساءً</li>
                    <li>يوم الجمعة: ٥ عصراً إلى ١١ مساءً</li>
                </ul>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label" for="appointment_date">التاريخ</label>
                        <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                               min="{{ date('Y-m-d') }}" required>
                        <div class="invalid-feedback" id="date-error"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label" for="appointment_time">الوقت المتاح</label>
                        <select class="form-select" id="appointment_time" name="appointment_time" required disabled>
                            <option value="">اختر التاريخ أولاً</option>
                        </select>
                        <div class="invalid-feedback" id="time-error"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-brush"></i>
                تفاصيل التصميم
            </h3>
            <div class="mb-4">
                <label class="form-label" for="notes">وصف التصميم المطلوب</label>
                <textarea class="form-control" id="notes" name="notes" rows="6"
                          placeholder="يرجى وصف التصميم المطلوب بالتفصيل (الألوان، القصة، التطريز، الأكمام، إلخ...)" required minlength="10"></textarea>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-geo-alt-fill"></i>
                مكان المقابلة
            </h3>
            <div class="mb-4">
                <p class="text-muted">
                    <i class="bi bi-info-circle me-2"></i>
                    للتصاميم المخصصة، تتم المقابلة في المحل فقط لضمان جودة الخدمة وتحقيق أفضل النتائج
                </p>
                <input type="hidden" name="location" value="store">
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-person-fill"></i>
                معلومات التواصل
            </h3>
            <div class="mb-4">
                <label class="form-label" for="phone">رقم الهاتف</label>
                <input type="tel" class="form-control" id="phone" name="phone"
                       value="{{ Auth::user()->phone ?? '' }}" required>
            </div>
        </div>

        <div class="d-grid gap-2 col-md-6 mx-auto mt-4">
            <button type="submit" class="btn btn-submit" id="submitBtn">
                <span class="loading-spinner spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <i class="bi bi-calendar-check me-2"></i>
                تأكيد حجز الموعد
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/customer/appointments.js') }}"></script>
@endsection
