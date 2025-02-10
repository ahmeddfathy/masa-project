@extends('layouts.customer')

@section('title', 'حجز موعد جديد')

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

    .custom-design-section {
        background: #f8f9fe;
        border: 2px solid #6c5ce7;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1rem;
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
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title mb-0">حجز موعد جديد</h2>
        <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-right"></i>
            العودة للمواعيد
        </a>
    </div>

    <form id="appointmentForm" action="{{ route('appointments.store') }}" method="POST" class="appointment-form">
        @csrf

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-grid-fill"></i>
                نوع الخدمة
            </h3>
            <div class="mb-4">
                <select class="form-select" name="service_type" id="service_type" required>
                    <option value="">اختر نوع الخدمة</option>
                    <option value="custom_design">تصميم مخصص</option>
                    <option value="new_abaya">عباية جديدة</option>
                    <option value="alteration">تعديل</option>
                    <option value="repair">إصلاح</option>
                </select>
                @error('service_type')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-calendar-fill"></i>
                موعد الزيارة
            </h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label" for="appointment_date">التاريخ</label>
                        <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                               min="{{ date('Y-m-d') }}" required>
                        @error('appointment_date')
                            <div class="error-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label class="form-label" for="appointment_time">الوقت</label>
                        <input type="time" class="form-control" id="appointment_time" name="appointment_time"
                               min="09:00" max="21:00" required>
                        @error('appointment_time')
                            <div class="error-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-geo-alt-fill"></i>
                معلومات الموقع
            </h3>
            <div class="mb-4">
                <label class="form-label">مكان الموعد</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="location" id="location_store"
                               value="store" checked>
                        <label class="form-check-label" for="location_store">
                            في المحل
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="location" id="location_client"
                               value="client_location">
                        <label class="form-check-label" for="location_client">
                            في موقع العميل
                        </label>
                    </div>
                </div>
                @error('location')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 d-none" id="addressField">
                <label class="form-label" for="address">العنوان</label>
                <textarea class="form-control" id="address" name="address" rows="3"
                          placeholder="يرجى إدخال العنوان بالتفصيل"></textarea>
                @error('address')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
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
                @error('phone')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">
                <i class="bi bi-card-text"></i>
                ملاحظات إضافية
            </h3>
            <div class="mb-4">
                <textarea class="form-control" id="notes" name="notes" rows="4"
                          placeholder="أضف أي ملاحظات أو تفاصيل إضافية هنا"></textarea>
                @error('notes')
                    <div class="error-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="custom-design-section d-none" id="customDesignSection">
            <h4 class="mb-3">
                <i class="bi bi-brush me-2"></i>
                تفاصيل التصميم المخصص
            </h4>
            <p class="text-muted mb-3">
                يرجى كتابة تفاصيل التصميم المطلوب بشكل واضح ودقيق.
                يمكنك إضافة المقاسات والألوان والتفاصيل الأخرى التي ترغب بها.
            </p>
        </div>

        <div class="d-grid gap-2 col-md-6 mx-auto mt-4">
            <button type="submit" class="btn btn-submit">
                <i class="bi bi-calendar-check me-2"></i>
                تأكيد حجز الموعد
            </button>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceTypeSelect = document.getElementById('service_type');
    const customDesignSection = document.getElementById('customDesignSection');
    const locationStore = document.getElementById('location_store');
    const locationClient = document.getElementById('location_client');
    const addressField = document.getElementById('addressField');
    const notesField = document.getElementById('notes');

    // Handle service type change
    serviceTypeSelect.addEventListener('change', function() {
        if (this.value === 'custom_design') {
            customDesignSection.classList.remove('d-none');
            notesField.setAttribute('required', 'required');
            notesField.setAttribute('minlength', '10');
        } else {
            customDesignSection.classList.add('d-none');
            notesField.removeAttribute('required');
            notesField.removeAttribute('minlength');
        }
    });

    // Handle location change
    function toggleAddress() {
        if (locationClient.checked) {
            addressField.classList.remove('d-none');
            document.getElementById('address').setAttribute('required', 'required');
        } else {
            addressField.classList.add('d-none');
            document.getElementById('address').removeAttribute('required');
        }
    }

    locationStore.addEventListener('change', toggleAddress);
    locationClient.addEventListener('change', toggleAddress);

    // Form validation
    const form = document.getElementById('appointmentForm');
    form.addEventListener('submit', function(e) {
        if (serviceTypeSelect.value === 'custom_design' && notesField.value.length < 10) {
            e.preventDefault();
            alert('يرجى إضافة تفاصيل كافية للتصميم المخصص (10 أحرف على الأقل)');
            notesField.focus();
        }
    });
});
</script>
@endsection
