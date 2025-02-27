<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="احجز جلسة تصوير مع عدسة سوما - باقات متنوعة لتصوير المواليد والأطفال والعائلات. خدمة احترافية وأسعار مناسبة في الرياض. احجز موعدك الآن!">
    <meta name="keywords" content="حجز تصوير، تصوير مواليد، تصوير أطفال، تصوير عائلي، استوديو تصوير، عدسة سوما، حجز موعد، الرياض">
    <meta name="author" content="عدسة سوما">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">

    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="عدسة سوما">
    <meta property="og:title" content="احجز جلسة تصوير مع عدسة سوما | تصوير مواليد وأطفال">
    <meta property="og:description" content="احجز جلسة تصوير مع عدسة سوما - باقات متنوعة لتصوير المواليد والأطفال والعائلات. خدمة احترافية وأسعار مناسبة في الرياض. احجز موعدك الآن!">
    <meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="احجز جلسة تصوير مع عدسة سوما | تصوير مواليد وأطفال">
    <meta name="twitter:description" content="احجز جلسة تصوير مع عدسة سوما - باقات متنوعة لتصوير المواليد والأطفال والعائلات. خدمة احترافية وأسعار مناسبة في الرياض. احجز موعدك الآن!">
    <meta name="twitter:image" content="{{ asset('assets/images/logo.png') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>احجز جلسة تصوير مع عدسة سوما | تصوير مواليد وأطفال في الرياض</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/booking.css') }}">
    <style>
        :root {
            --primary-color: #21B3B0 !important;
            --secondary-color: #21B3B0 !important;
            --primary-gradient: linear-gradient(45deg, #21B3B0, #21B3B0) !important;
            --secondary-gradient: linear-gradient(45deg, #21B3B0, #21B3B0) !important;
            --navbar-bg: #21B3B0 !important;
            --select-focus: #21B3B0 !important;
        }

        .btn-primary {
            background: #21B3B0 !important;
        }

        .btn-primary:hover {
            background: #219376 !important;
        }

        .package-card::before {
            background: #21B3B0 !important;
        }

        .package-card.selected {
            border-color: #21B3B0 !important;
        }

        .booking-form h2 {
            color: #21B3B0 !important;
        }

        .booking-form h2::after {
            background: #21B3B0 !important;
        }
    </style>
</head>
<body>
    @include('parts.navbar')

    <div class="container py-4">
        <!-- Error and Success Messages -->
        @if(session('error') || session('success') || $errors->any())
        <div class="messages-container mb-4">
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6 class="alert-heading mb-2"><i class="fas fa-exclamation-triangle me-2"></i>يوجد أخطاء في النموذج:</h6>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
        @endif

        <!-- Gallery Carousel -->
        <div id="galleryCarousel" class="carousel slide gallery-carousel animate-fadeInUp" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($galleryImages as $key => $image)
                <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="{{ $key }}"
                        class="{{ $key === 0 ? 'active' : '' }}" aria-current="{{ $key === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $key + 1 }}"></button>
                @endforeach
            </div>

            <div class="carousel-inner">
                @foreach($galleryImages as $key => $image)
                <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                    <img src="{{ Storage::url($image->image_url) }}" class="d-block w-100" alt="Gallery Image">
                </div>
                @endforeach
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">السابق</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">التالي</span>
            </button>
        </div>

        <!-- Booking Form -->
        <div class="booking-form animate-fadeInUp">
            <h2>حجز جلسة تصوير</h2>

            <form action="{{ route('client.bookings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="intended_route" value="{{ url()->current() }}">

                <!-- Service Selection -->
                <div class="mb-4">
                    <label class="form-label">نوع الجلسة</label>
                    <select name="service_id" class="form-select" required>
                        <option value="">اختر نوع الجلسة</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Package Selection -->
                <div class="mb-4">
                    <label class="form-label">الباقة</label>
                    <div class="row">
                        @foreach($packages as $package)
                            <div class="col-md-6">
                                <div class="package-card {{ old('package_id') == $package->id ? 'selected' : '' }}">
                                    <input type="radio" name="package_id" value="{{ $package->id }}"
                                           class="form-check-input package-select" required
                                           {{ old('package_id') == $package->id ? 'checked' : '' }}>
                                    <h5>{{ $package->name }}</h5>
                                    <p class="text-muted">{{ $package->description }}</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-clock me-2"></i>المدة:
                                            @if($package->duration >= 60)
                                                {{ floor($package->duration / 60) }} ساعة
                                                @if($package->duration % 60 > 0)
                                                    و {{ $package->duration % 60 }} دقيقة
                                                @endif
                                            @else
                                                {{ $package->duration }} دقيقة
                                            @endif
                                        </li>
                                        <li><i class="fas fa-images me-2"></i>عدد الصور: {{ $package->num_photos }}</li>
                                        <li><i class="fas fa-palette me-2"></i>عدد الثيمات: {{ $package->themes_count }}</li>
                                        <li><i class="fas fa-tag me-2"></i>السعر: {{ $package->base_price }} درهم</li>
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Addons Selection -->
                <div class="mb-4" id="addons-section">
                    <label class="form-label">الإضافات المتاحة</label>
                    <div class="row">
                        @foreach($addons as $addon)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input type="checkbox" name="addons[{{ $addon->id }}][id]"
                                                   value="{{ $addon->id }}"
                                                   class="form-check-input"
                                                   id="addon-{{ $addon->id }}"
                                                   {{ old('addons.'.$addon->id.'.id') ? 'checked' : '' }}>
                                            <input type="hidden" name="addons[{{ $addon->id }}][quantity]" value="1">
                                            <label class="form-check-label" for="addon-{{ $addon->id }}">
                                                <h6>{{ $addon->name }}</h6>
                                                <p class="text-muted small mb-2">{{ $addon->description }}</p>
                                                <span class="badge bg-primary">{{ $addon->price }} درهم</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Date and Time -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">تاريخ الجلسة</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" name="session_date" class="form-control" required
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                   value="{{ old('session_date') }}">
                        </div>
                        <small class="text-muted mt-1">
                            <i class="fas fa-info-circle"></i>
                            يمكنك اختيار موعد من الغد وحتى 30 يوم قادمة
                        </small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">وقت الجلسة</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <select name="session_time" class="form-select" required id="sessionTime" disabled>
                                <option value="">يرجى اختيار الباقة والتاريخ أولاً</option>
                            </select>
                        </div>
                        <small class="text-muted mt-1" id="timeNote">
                            <i class="fas fa-info-circle"></i>
                            سيتم عرض المواعيد المتاحة بعد اختيار الباقة والتاريخ
                        </small>
                    </div>
                </div>

                <!-- Baby Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">اسم المولود</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-baby"></i></span>
                            <input type="text" name="baby_name" class="form-control" value="{{ old('baby_name') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">تاريخ الميلاد</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                            <input type="date" name="baby_birth_date" class="form-control" value="{{ old('baby_birth_date') }}">
                        </div>
                    </div>
                </div>

                <!-- Gender -->
                <div class="mb-4">
                    <label class="form-label">الجنس</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                        <select name="gender" class="form-select">
                            <option value="">اختر الجنس</option>
                            <option value="ذكر" {{ old('gender') == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                            <option value="أنثى" {{ old('gender') == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="form-label">ملاحظات إضافية</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-comment"></i></span>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Consent Checkboxes -->
                <div class="mb-4">
                    <div class="mb-3">
                        <label class="form-label">الموافقة على عرض الصور</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-camera"></i></span>
                            <select name="image_consent" class="form-select" id="imageConsent">
                                <option value="1" {{ old('image_consent') == '1' ? 'selected' : '' }}>نعم، أوافق على عرض الصور في معرض الاستوديو ومواقع التواصل الاجتماعي</option>
                                <option value="0" {{ old('image_consent') == '0' ? 'selected' : '' }}>لا، لا أوافق على عرض الصور</option>
                            </select>
                        </div>
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="fas fa-gift me-1"></i>
                                في حالة الموافقة على عرض الصور، ستحصل على ثيم إضافي مجاناً كهدية شكر
                            </small>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="terms_consent" class="form-check-input" id="termsConsent"
                               value="1" required {{ old('terms_consent') ? 'checked' : '' }}>
                        <label class="form-check-label" for="termsConsent">
                            أوافق على <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">الشروط والسياسات</a> الخاصة بالاستوديو <span class="text-danger">*</span>
                        </label>
                    </div>
                </div>

                <!-- تعليمات الحجز والدفع -->
                @auth
                <div class="mb-4 text-center">
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3">تعليمات الحجز والدفع:</h6>
                                    <ul class="list-unstyled text-start mb-0">
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            بعد تأكيد الحجز، سيتم إرسال رقم الحساب البنكي إليك
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            يرجى تحويل المبلغ المطلوب وإرسال صورة الإيصال على الواتساب
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            سيتم تأكيد الحجز نهائياً بعد استلام إيصال التحويل
                                        </li>
                                        <li>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            يمكنك متابعة حالة حجزك من صفحة حجوزاتي
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth

                <!-- Submit Button -->
                <div class="text-center">
                    @auth
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-check me-2"></i>تأكيد الحجز
                        </button>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                سيتم إرسال تفاصيل التحويل البنكي بعد تأكيد الحجز
                            </small>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            يرجى <a href="{{ route('login') }}">تسجيل الدخول</a> أو <a href="{{ route('register') }}">إنشاء حساب جديد</a> للمتابعة
                        </div>
                    @endauth
                </div>

            </form>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">الشروط والسياسات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>سياسة عرض الصور:</h6>
                    <ul>
                        <li>يحتفظ الاستوديو بحق عرض الصور المختارة في معرض الصور الخاص به.</li>
                        <li>سيتم استخدام الصور في وسائل التواصل الاجتماعي والمواد التسويقية للاستوديو.</li>
                        <li>نحن نحترم خصوصيتكم ولن نستخدم الصور بطريقة غير لائقة.</li>
                    </ul>

                    <h6 class="mt-4">الشروط العامة:</h6>
                    <ul>
                        <li>يجب الحضور في الموعد المحدد بدقة.</li>
                        <li>في حالة الرغبة في إلغاء الحجز، يجب إخطارنا قبل 24 ساعة على الأقل.</li>
                        <li>سيتم تسليم الصور النهائية خلال أسبوع من تاريخ الجلسة.</li>
                        <li>يتم دفع 50% من قيمة الحجز مقدماً لتأكيد الموعد.</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store all data with proper JSON encoding
            const allServices = JSON.parse('{!! addslashes(json_encode($services)) !!}');
            const allPackages = JSON.parse('{!! addslashes(json_encode($packages)) !!}');
            const allAddons = JSON.parse('{!! addslashes(json_encode($addons)) !!}');
            const currentBookings = JSON.parse('{!! addslashes(json_encode($currentBookings)) !!}');

            // Get DOM elements
            const serviceSelect = document.querySelector('select[name="service_id"]');
            const packagesContainer = document.querySelector('.row:has(.package-card)');
            const addonsSection = document.getElementById('addons-section');

            // Hide packages and addons initially
            packagesContainer.style.display = 'none';
            addonsSection.style.display = 'none';

            function updateAvailableTimes(packageDuration) {
                const sessionTimeSelect = document.getElementById('sessionTime');
                const sessionDateInput = document.querySelector('input[name="session_date"]');
                const timeNote = document.getElementById('timeNote');
                const selectedPackageRadio = document.querySelector('.package-select:checked');
                const selectedServiceId = document.querySelector('select[name="service_id"]').value;

                // التحقق من وجود جميع العناصر المطلوبة
                if (!sessionTimeSelect || !sessionDateInput || !timeNote || !selectedPackageRadio || !selectedServiceId) {
                    console.error('Required elements not found');
                    return;
                }

                // التحقق من وجود قيم صالحة
                if (!packageDuration || !sessionDateInput.value || !selectedPackageRadio.value) {
                    sessionTimeSelect.disabled = true;
                    sessionTimeSelect.innerHTML = '<option value="">يرجى اختيار الباقة والتاريخ أولاً</option>';
                    timeNote.innerHTML = `
                        <i class="fas fa-info-circle"></i>
                        يرجى اختيار الباقة والتاريخ أولاً
                    `;
                    return;
                }

                // عرض حالة التحميل
                sessionTimeSelect.disabled = true;
                sessionTimeSelect.innerHTML = '<option value="">جاري تحميل المواعيد المتاحة...</option>';
                timeNote.innerHTML = `
                    <i class="fas fa-spinner fa-spin"></i>
                    جاري التحقق من المواعيد المتاحة...
                `;

                // تنسيق التاريخ
                const formattedDate = sessionDateInput.value.split('T')[0];

                // التحقق من وجود CSRF token
                const tokenElement = document.querySelector('meta[name="csrf-token"]');
                if (!tokenElement) {
                    console.error('CSRF token not found');
                    timeNote.innerHTML = `
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        حدث خطأ في النظام. يرجى تحديث الصفحة والمحاولة مرة أخرى
                    `;
                    return;
                }

                const token = tokenElement.getAttribute('content');

                // تجهيز البيانات
                const requestData = {
                    date: formattedDate,
                    package_id: selectedPackageRadio.value,
                    service_id: selectedServiceId
                };

                // تنفيذ الطلب
                fetch('/client/bookings/available-slots', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(requestData)
                })
                .then(async response => {
                    const responseText = await response.text();
                    console.log('Raw response:', responseText);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}, response: ${responseText}`);
                    }

                    try {
                        return JSON.parse(responseText);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw new Error('Invalid JSON response');
                    }
                })
                .then(data => {
                    console.log('Response data:', data);
                    sessionTimeSelect.innerHTML = '';
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'اختر الوقت المناسب';
                    sessionTimeSelect.appendChild(defaultOption);

                    if (data.status === 'success') {
                        let alertHtml = '';

                        // إذا كانت هناك باقات بديلة متاحة
                        if (data.slots && data.slots.has_alternative_packages &&
                            data.slots.alternative_packages &&
                            data.slots.alternative_packages.length > 0) {
                            let alternativePackagesHtml = '';
                            let hasAnyValidAlternative = false;

                            data.slots.alternative_packages.forEach(alt => {
                                const pkg = alt.package;
                                // فقط إذا كانت هناك مواعيد متاحة للباقة البديلة
                                if (alt.available_slots && alt.available_slots.length > 0) {
                                    hasAnyValidAlternative = true;
                                    alternativePackagesHtml += `
                                        <div class="alternative-package mb-2">
                                            <h6>${pkg.name}</h6>
                                            <p class="small text-muted mb-1">${pkg.description}</p>
                                            <ul class="list-unstyled small">
                                                <li><i class="fas fa-clock me-1"></i>المدة: ${
                                                    pkg.duration >= 60
                                                    ? `${Math.floor(pkg.duration / 60)} ساعة${pkg.duration % 60 > 0 ? ` و ${pkg.duration % 60} دقيقة` : ''}`
                                                    : `${pkg.duration} دقيقة`
                                                }</li>
                                                <li><i class="fas fa-tag me-1"></i>السعر: ${pkg.base_price} درهم</li>
                                                <li><i class="fas fa-calendar-check me-1"></i>المواعيد المتاحة: ${alt.available_slots.length}</li>
                                            </ul>
                                            <button onclick="selectPackage(${pkg.id}, ${selectedServiceId})" class="btn btn-warning btn-sm">
                                                <i class="fas fa-exchange-alt me-1"></i>
                                                اختيار هذه الباقة
                                            </button>
                                        </div>
                                    `;
                                }
                            });

                            // فقط عرض قسم الباقات البديلة إذا وجدت باقات متاحة فعلاً
                            if (hasAnyValidAlternative) {
                                alertHtml += `
                                    <div class="alert alert-info mb-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        لا تتوفر مواعيد لهذه الباقة حالياً، ولكن هناك باقات متاحة في نفس الخدمة:
                                        <div class="mt-2">
                                            ${alternativePackagesHtml}
                                        </div>
                                    </div>
                                `;
                            }
                        }

                        // إذا كان هناك يوم متاح قادم
                        if (data.slots && data.slots.next_available_date) {
                            alertHtml += `
                                <div class="alert alert-warning">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    أقرب موعد متاح هو يوم ${data.slots.next_available_formatted_date}
                                    <button onclick="selectDate('${data.slots.next_available_date}')" class="btn btn-warning btn-sm float-end">
                                        اختيار هذا اليوم
                                    </button>
                                </div>
                            `;
                        }

                        // إضافة الرسائل إلى الصفحة
                        if (alertHtml) {
                            const timeContainer = sessionTimeSelect.closest('.col-md-6');
                            if (timeContainer.querySelector('.alert')) {
                                timeContainer.querySelectorAll('.alert').forEach(alert => alert.remove());
                            }
                            timeContainer.insertAdjacentHTML('afterbegin', alertHtml);
                        }

                        // عرض المواعيد المتاحة في اليوم المحدد
                        if (Array.isArray(data.slots) && data.slots.length > 0) {
                            data.slots.forEach(slot => {
                                const option = document.createElement('option');
                                option.value = slot.time;
                                option.textContent = `${slot.formatted_time} (${slot.time} - ${slot.end_time})`;
                                sessionTimeSelect.appendChild(option);
                            });

                            sessionTimeSelect.disabled = false;
                            timeNote.innerHTML = `
                                <i class="fas fa-info-circle"></i>
                                المواعيد المتاحة تأخذ في الاعتبار مدة الجلسة (${
                                    packageDuration >= 60
                                    ? `${Math.floor(packageDuration / 60)} ساعة${packageDuration % 60 > 0 ? ` و ${packageDuration % 60} دقيقة` : ''}`
                                    : `${packageDuration} دقيقة`
                                })
                            `;

                            // إزالة أي alert سابق
                            const timeContainer = sessionTimeSelect.closest('.col-md-6');
                            if (timeContainer.querySelector('.alert')) {
                                timeContainer.querySelector('.alert').remove();
                            }
                        } else {
                            sessionTimeSelect.disabled = true;
                            timeNote.innerHTML = `
                                <i class="fas fa-exclamation-circle text-danger"></i>
                                لا توجد مواعيد متاحة في هذا اليوم
                            `;
                        }
                    } else {
                        sessionTimeSelect.disabled = true;
                        timeNote.innerHTML = `
                            <i class="fas fa-exclamation-circle text-danger"></i>
                            ${data.message || 'حدث خطأ أثناء تحميل المواعيد المتاحة'}
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    sessionTimeSelect.disabled = true;
                    sessionTimeSelect.innerHTML = '<option value="">حدث خطأ أثناء تحميل المواعيد</option>';
                    timeNote.innerHTML = `
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        حدث خطأ أثناء تحميل المواعيد المتاحة. يرجى المحاولة مرة أخرى
                    `;
                });
            }

            // Handle package selection
            function handlePackageSelection(packageId) {
                const sessionTimeSelect = document.getElementById('sessionTime');
                const sessionDateInput = document.querySelector('input[name="session_date"]');

                if (!packageId) {
                    addonsSection.style.display = 'none';
                    sessionTimeSelect.disabled = true;
                    sessionTimeSelect.innerHTML = '<option value="">يرجى اختيار الباقة أولاً</option>';
                    return;
                }

                // Find selected package
                const selectedPackage = allPackages.find(pkg => pkg.id == packageId);
                if (!selectedPackage) {
                    addonsSection.style.display = 'none';
                    sessionTimeSelect.disabled = true;
                    return;
                }

                // Enable time selection if date is selected
                if (sessionDateInput.value) {
                    sessionTimeSelect.disabled = false;
                    updateAvailableTimes(selectedPackage.duration);
                }

                // Add date change listener for this package
                sessionDateInput.addEventListener('change', function() {
                    if (this.value) {
                        sessionTimeSelect.disabled = false;
                        updateAvailableTimes(selectedPackage.duration);
                    } else {
                        sessionTimeSelect.disabled = true;
                        sessionTimeSelect.innerHTML = '<option value="">يرجى اختيار التاريخ أولاً</option>';
                    }
                });

                // Update addons display
                if (selectedPackage.addons && selectedPackage.addons.length) {
                    const addonsContainer = addonsSection.querySelector('.row');
                    addonsContainer.innerHTML = selectedPackage.addons.map(addon => `
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input type="checkbox" name="addons[${addon.id}][id]"
                                               value="${addon.id}"
                                               class="form-check-input"
                                               id="addon-${addon.id}">
                                        <input type="hidden" name="addons[${addon.id}][quantity]" value="1">
                                        <label class="form-check-label" for="addon-${addon.id}">
                                            <h6>${addon.name}</h6>
                                            <p class="text-muted small mb-2">${addon.description}</p>
                                            <span class="badge bg-primary">${addon.price} درهم</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');
                    addonsSection.style.display = 'block';
                }
            }

            // Handle service selection
            serviceSelect.addEventListener('change', function() {
                const selectedServiceId = this.value;
                if (!selectedServiceId) {
                    packagesContainer.style.display = 'none';
                    addonsSection.style.display = 'none';
                    return;
                }

                // Filter packages for selected service
                const servicePackages = allPackages.filter(pkg =>
                    pkg.service_ids.includes(parseInt(selectedServiceId))
                );

                // Update packages display
                packagesContainer.innerHTML = servicePackages.map(pkg => `
                    <div class="col-md-6">
                        <div class="package-card">
                            <input type="radio" name="package_id" value="${pkg.id}"
                                   class="form-check-input package-select" required>
                            <h5>${pkg.name}</h5>
                            <p class="text-muted">${pkg.description}</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-clock me-2"></i>المدة: ${
                                    pkg.duration >= 60
                                    ? `${Math.floor(pkg.duration / 60)} ساعة${pkg.duration % 60 > 0 ? ` و ${pkg.duration % 60} دقيقة` : ''}`
                                    : `${pkg.duration} دقيقة`
                                }</li>
                                <li><i class="fas fa-images me-2"></i>عدد الصور: ${pkg.num_photos}</li>
                                <li><i class="fas fa-palette me-2"></i>عدد الثيمات: ${pkg.themes_count}</li>
                                <li><i class="fas fa-tag me-2"></i>السعر: ${pkg.base_price} درهم</li>
                            </ul>
                        </div>
                    </div>
                `).join('');

                packagesContainer.style.display = 'flex';
                addonsSection.style.display = 'none';

                // Reattach package selection event listeners
                attachPackageListeners();
            });

            function attachPackageListeners() {
                document.querySelectorAll('.package-card').forEach(card => {
                    card.addEventListener('click', function() {
                        const radio = this.querySelector('input[type="radio"]');
                        if (radio) {
                            radio.checked = true;
                            handlePackageSelection(radio.value);
                        }
                        document.querySelectorAll('.package-card').forEach(c => {
                            c.classList.remove('selected');
                        });
                        this.classList.add('selected');
                    });
                });
            }

            // Check if a package is already selected (e.g. after form validation error)
            const selectedPackageRadio = document.querySelector('.package-select:checked');
            if (selectedPackageRadio) {
                handlePackageSelection(selectedPackageRadio.value);
                selectedPackageRadio.closest('.package-card').classList.add('selected');
            }

            // Add date change listener
            document.querySelector('input[name="session_date"]').addEventListener('change', function() {
                const selectedPackageRadio = document.querySelector('.package-select:checked');
                if (selectedPackageRadio) {
                    const packageDuration = parseFloat(selectedPackageRadio.closest('.package-card').querySelector('.duration-value').textContent);
                    if (this.value) {
                        document.getElementById('sessionTime').disabled = false;
                        updateAvailableTimes(packageDuration);
                    } else {
                        document.getElementById('sessionTime').disabled = true;
                    }
                }
            });

            // Form Animation
            document.querySelectorAll('.form-control, .form-select').forEach(element => {
                element.addEventListener('focus', function() {
                    this.closest('.input-group')?.classList.add('focused');
                });
                element.addEventListener('blur', function() {
                    this.closest('.input-group')?.classList.remove('focused');
                });
            });

            // تحديث دالة selectDate لتقوم باختيار التاريخ تلقائياً
            window.selectDate = function(date) {
                const dateInput = document.querySelector('input[name="session_date"]');
                dateInput.value = date;
                dateInput.dispatchEvent(new Event('change'));

                // إزالة رسائل التنبيه بعد اختيار التاريخ
                const timeContainer = document.getElementById('sessionTime').closest('.col-md-6');
                const alerts = timeContainer.querySelectorAll('.alert');
                alerts.forEach(alert => alert.remove());
            }

            // تحديث دالة selectPackage لتقوم باختيار الباقة تلقائياً
            window.selectPackage = function(packageId, serviceId) {
                const packageRadio = document.querySelector(`input[name="package_id"][value="${packageId}"]`);
                if (packageRadio) {
                    // تحديد الباقة
                    packageRadio.checked = true;

                    // إضافة class selected للباقة المختارة وإزالته من الباقي
                    document.querySelectorAll('.package-card').forEach(card => {
                        card.classList.remove('selected');
                    });
                    packageRadio.closest('.package-card').classList.add('selected');

                    // تحديد الخدمة المناسبة إذا لم تكن محددة
                    const serviceSelect = document.querySelector('select[name="service_id"]');
                    if (serviceSelect.value !== serviceId.toString()) {
                        serviceSelect.value = serviceId;
                        serviceSelect.dispatchEvent(new Event('change'));
                    }

                    // تشغيل معالج اختيار الباقة
                    handlePackageSelection(packageId);

                    // إزالة رسائل التنبيه
                    const timeContainer = document.getElementById('sessionTime').closest('.col-md-6');
                    const alerts = timeContainer.querySelectorAll('.alert');
                    alerts.forEach(alert => alert.remove());

                    // تمرير للباقة المختارة في الصفحة
                    packageRadio.closest('.package-card').scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        });
    </script>
</body>
</html>
