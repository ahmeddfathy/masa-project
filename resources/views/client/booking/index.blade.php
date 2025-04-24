<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="احجز جلسة تصوير مع عدسة سوما - باقات متنوعة لتصوير المواليد والأطفال والعائلات. خدمة احترافية وأسعار مناسبة في أبها، حي المحالة. احجز موعدك الآن!">
    <meta name="keywords" content="حجز تصوير، تصوير مواليد، تصوير أطفال، تصوير عائلي، استوديو تصوير، عدسة سوما، حجز موعد، أبها، محالة">
    <meta name="author" content="عدسة سوما">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">

    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="عدسة سوما">
    <meta property="og:title" content="احجز جلسة تصوير مع عدسة سوما | تصوير مواليد وأطفال في أبها">
    <meta property="og:description" content="احجز جلسة تصوير مع عدسة سوما - باقات متنوعة لتصوير المواليد والأطفال والعائلات. خدمة احترافية وأسعار مناسبة في أبها، حي المحالة. احجز موعدك الآن!">
    <meta property="og:image" content="/assets/images/logo.png" loading="lazy">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="احجز جلسة تصوير مع عدسة سوما | تصوير مواليد وأطفال في أبها">
    <meta name="twitter:description" content="احجز جلسة تصوير مع عدسة سوما - باقات متنوعة لتصوير المواليد والأطفال والعائلات. خدمة احترافية وأسعار مناسبة في أبها، حي المحالة. احجز موعدك الآن!">
    <meta name="twitter:image" content="{{ asset('assets/images/logo.png') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>احجز جلسة تصوير مع عدسة سوما | تصوير مواليد وأطفال في أبها، حي المحالة</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/style.css') }}?t={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/booking.css') }}?t={{ time() }}">

    <style>
        /* أنماط السعر والكوبون */
        .old-price {
            text-decoration: line-through;
            color: #999;
            margin-right: 5px;
        }

        .new-price {
            color: #ff6b6b;
            font-weight: bold;
            margin-right: 5px;
        }

        .coupon-label {
            background-color: #ff6b6b;
            color: white;
            border-radius: 4px;
            padding: 2px 5px;
            font-size: 0.75rem;
            white-space: nowrap;
            display: inline-block;
            margin-top: 5px;
        }

        .coupon-label i {
            margin-right: 3px;
        }

        /* تأثير نبض للكوبون */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .coupon-label {
            animation: pulse 1.5s infinite;
        }

        /* نمط عرض الكوبون كعنصر خاص */
        .coupon-info {
            margin-top: 5px;
            padding: 3px 0;
            color: #ff6b6b;
        }

        .coupon-icon {
            color: #ff6b6b;
            animation: pulse 1.5s infinite;
        }

        .coupon-code {
            background-color: #ff6b6b;
            color: white;
            border-radius: 4px;
            padding: 2px 5px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .discount-value {
            margin-right: 5px;
            font-weight: bold;
        }
    </style>

    <!-- Tabby Scripts -->
    <script src="https://checkout.tabby.ai/tabby-promo.js"></script>
    <script>
        // تجهيز متغيرات للتابي
        window.packageData = {
            price: 0
        };
    </script>


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

            @if(session('retry_payment'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>يمكنك متابعة عملية الحجز مع إعادة محاولة الدفع. تم الاحتفاظ ببياناتك السابقة.
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
                    <img src="{{ url('storage/' . $image->image_url) }}"
                         class="d-block w-100"
                         alt="Gallery Image"
                         loading="{{ $key === 0 ? 'eager' : 'lazy' }}">
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

        <!-- Authentication Notice -->
        @guest
        <div class="auth-notice animate-fadeInUp mb-4">
            <div class="alert alert-booking">
                <div class="alert-icon">
                    <i class="fas fa-user-lock"></i>
                </div>
                <div class="alert-content">
                    <h5>تنبيه هام</h5>
                    <p>قبل البدء في تعبئة نموذج الحجز، يجب عليك <a href="{{ route('login') }}">تسجيل الدخول</a> إلى حسابك أو <a href="{{ route('register') }}">إنشاء حساب جديد</a> إذا لم يكن لديك حساب مسبقاً.</p>
                </div>
            </div>
        </div>
        @endguest

        <!-- Booking Form -->
        <div class="booking-form animate-fadeInUp">
            <h2>حجز جلسة تصوير</h2>

            <form action="{{ route('client.bookings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="intended_route" value="{{ url()->current() }}">

                <!-- Add this hidden input for storing the old package id from server side -->
                <input type="hidden" id="old-package-id" value="{{ old('package_id', 0) }}">

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
                                        <li><i class="fas fa-tag me-2"></i>السعر: {{ $package->base_price }} ريال</li>
                                        @if(isset($package->best_coupon))
                                        <li class="coupon-info">
                                            <i class="fas fa-ticket-alt me-2 coupon-icon"></i>
                                            كوبون خصم:
                                            <span class="coupon-code">{{ $package->best_coupon->code }}</span>
                                            <span class="discount-value">({{ $package->discount_text }})</span>
                                        </li>
                                        @endif
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
                                                <span class="badge bg-primary">${addon.price} ريال</span>
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
                            أوافق على <a href="{{ route('policy') }}">الشروط والسياسات</a> الخاصة بالاستوديو <span class="text-danger">*</span>
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
                                    <ul class="list-unstyled text-start mb-4">
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            سيتم تحويلك إلى بوابة الدفع الإلكتروني بعد تأكيد الحجز
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-credit-card text-success me-2"></i>
                                            يمكنك الدفع باستخدام بطاقة مدى أو فيزا أو ماستركارد
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-lock text-success me-2"></i>
                                            جميع عمليات الدفع آمنة ومشفرة بواسطة PayTabs
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            سيتم تأكيد الحجز تلقائيًا بعد نجاح عملية الدفع
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

                <!-- Coupon Code Section -->
                <div class="coupon-container">
                    <div class="coupon-header">
                        <div class="coupon-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <h5>كود الخصم</h5>
                    </div>
                    <div class="coupon-body">
                        <div class="coupon-input-wrapper">
                            <div class="coupon-input-container">
                                <i class="fas fa-tag input-icon"></i>
                                <input type="text" name="coupon_code" id="coupon_code" class="coupon-input" placeholder="أدخل كود الخصم إذا كان متوفراً لديك">
                                <button type="button" id="check-coupon" class="verify-btn">
                                    <span class="verify-text">تحقق</span>
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                            <div id="coupon-message" class="coupon-message"></div>
                        </div>
                        <div id="coupon-details" class="coupon-details d-none">
                            <div class="applied-coupon">
                                <div class="coupon-badge">
                                    <i class="fas fa-check-circle"></i>
                                    <span id="coupon-code-display"></span>
                                </div>
                                <div id="coupon-discount-display" class="discount-text"></div>
                                <button type="button" id="remove-coupon" class="remove-coupon-btn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Breakdown Section -->
                <div class="price-breakdown-container" id="price-breakdown-section" style="display:none;">
                    <div class="price-breakdown-header">
                        <div class="price-icon-wrapper">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h5>تفاصيل السعر</h5>
                    </div>

                    <div class="price-breakdown-body">
                        <div class="price-row">
                            <div class="price-label">السعر الأصلي</div>
                            <div class="price-value" id="original-price-display">0 ريال</div>
                        </div>

                        <div class="price-row discount-row">
                            <div class="price-label">
                                <i class="fas fa-percentage pulse-icon"></i>
                                قيمة الخصم
                            </div>
                            <div class="price-value discount-value" id="discount-amount-display">0 ريال</div>
                        </div>

                        <div class="price-row total-row">
                            <div class="price-label">السعر النهائي بعد الخصم</div>
                            <div class="price-value final-price" id="final-price-display">0 ريال</div>
                        </div>

                        <div class="total-savings">
                            <div class="savings-badge">
                                <i class="fas fa-coins me-2"></i>
                                <span>وفرت</span>
                                <span id="savings-percentage">0%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Section -->
                @auth
                <div class="payment-methods-section mb-4">
                    <h4 class="mb-3">اختر طريقة الدفع</h4>

                    <!-- إضافة تنبيه توضيحي عن وضع الاختبار لتابي -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ملاحظة هامة:</strong> حالياً، خدمة الدفع عبر تابي تعمل في وضع الاختبار فقط ولن يتم خصم أي مبالغ من بطاقتك.
                    </div>

                    <div class="payment-methods-container">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="payment-method-card">
                                    <input type="radio" name="payment_method" id="payment_tabby" value="tabby" checked>
                                    <label for="payment_tabby" class="payment-method-label tabby-shimmer">
                                        <div class="payment-icon">
                                            <img src="https://th.bing.com/th/id/OIP.MYBQ1iOEIlhyysL0Y3eh4wHaFG?rs=1&pid=ImgDetMain" alt="Tabby" style="height: 30px;">
                                        </div>
                                        <div class="payment-details">
                                            <h5>التقسيط بدون فوائد</h5>
                                            <p>قسّم على 4 دفعات شهرية بدون فوائد مع تابي</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="payment-method-card">
                                    <input type="radio" name="payment_method" id="payment_cod" value="cod">
                                    <label for="payment_cod" class="payment-method-label">
                                        <div class="payment-icon">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="payment-details">
                                            <h5>الدفع عند الاستلام</h5>
                                            <p>ادفع نقداً بعد حضور الجلسة</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabby Container -->
                    <div id="tabby-container" style="display: none;">
                        <div class="tabby-promo">
                            <img src="https://th.bing.com/th/id/OIP.MYBQ1iOEIlhyysL0Y3eh4wHaFG?rs=1&pid=ImgDetMain" alt="Tabby" class="tabby-logo">
                            <h4>تقسيط بدون فوائد أو رسوم إضافية</h4>
                        </div>
                        <div class="tabby-info">
                            <p>مع تابي، يمكنك دفع ربع المبلغ الآن والباقي على 3 أشهر بدون فوائد أو رسوم إضافية.
                            كل ما تحتاجه هو بطاقة مدى أو بطاقة ائتمان (فيزا/ماستركارد) سعودية.</p>
                            <p>سيتم تحويلك إلى موقع تابي لإتمام عملية التقسيط بعد تأكيد الحجز.</p>
                        </div>

                        <!-- Tabby Product Widget - للمنتج الحالي -->
                        <div id="tabby-product-widget"></div>

                        <div class="tabby-disclaimer">
                            <p class="small text-muted">يرجى التأكد من إدخال بيانات دقيقة وصحيحة لإتمام عملية الدفع بنجاح.</p>
                        </div>
                        <figure class="tabby-example">
                            <img src="https://mintlify.s3.us-west-1.amazonaws.com/tabby-5f40add6/images/tabby-payment-method.png" alt="شاشة تابي" />
                            <figcaption class="small">شكل شاشة تابي عند الدفع</figcaption>
                        </figure>
                    </div>
                </div>
                @endauth

                <!-- Submit Button -->
                <div class="text-center">
                    @auth
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-credit-card me-2"></i>متابعة للدفع
                        </button>
                        <div class="mt-3">
                            <small class="text-muted">
                                سيتم تحويلك إلى صفحة الدفع الآمنة بعد تأكيد الحجز
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

    <!-- Add Tabby JavaScript functionality at the end of the file, before closing body tag -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Store all data with proper JSON encoding
            const allServices = JSON.parse('{!! addslashes(json_encode($services)) !!}');
            const allPackages = JSON.parse('{!! addslashes(json_encode($packages)) !!}');
            const allAddons = JSON.parse('{!! addslashes(json_encode($addons)) !!}');
            const currentBookings = JSON.parse('{!! addslashes(json_encode($currentBookings)) !!}');

            // Store old session time if available
            const oldSessionTime = '{{ old("session_time") }}';
            // Get old package id from hidden input
            const oldPackageId = document.getElementById('old-package-id').value;

            // Get DOM elements
            const serviceSelect = document.querySelector('select[name="service_id"]');
            const packagesContainer = document.querySelector('.row:has(.package-card)');
            const addonsSection = document.getElementById('addons-section');
            const sessionDateInput = document.querySelector('input[name="session_date"]');

            // Hide packages and addons initially
            packagesContainer.style.display = 'none';
            addonsSection.style.display = 'none';

            function updateAvailableTimes(packageDuration) {
                const sessionTimeSelect = document.getElementById('sessionTime');
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
                                                <li><i class="fas fa-tag me-1"></i>السعر: ${pkg.base_price} ريال</li>
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
                                // تحديد الوقت السابق إذا كان موجوداً
                                if (oldSessionTime && oldSessionTime === slot.time) {
                                    option.selected = true;
                                }
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
                const selectedPackage = allPackages.find(pkg => pkg.id == packageId);
                if (!selectedPackage) {
                    addonsSection.style.display = 'none';
                    document.getElementById('sessionTime').disabled = true;
                    document.getElementById('sessionTime').innerHTML = '<option value="">يرجى اختيار الباقة أولاً</option>';
                    return;
                }

                // Remove any applied coupon when package is changed
                if (document.getElementById('coupon-details') && !document.getElementById('coupon-details').classList.contains('d-none')) {
                    removeCoupon();
                }

                // Enable time selection if date is selected
                const sessionTimeSelect = document.getElementById('sessionTime');
                const sessionDateInput = document.querySelector('input[name="session_date"]');

                if (sessionDateInput.value) {
                    sessionTimeSelect.disabled = false;
                    updateAvailableTimes(selectedPackage.duration);
                }

                // تفريغ قسم الإضافات أولاً
                const addonsContainer = addonsSection.querySelector('.row');
                addonsContainer.innerHTML = '';

                // Update addons display
                if (selectedPackage.addons && selectedPackage.addons.length) {
                    // إنشاء الإضافات المرتبطة بالباقة المحددة فقط
                    addonsContainer.innerHTML = selectedPackage.addons.map(addon => `
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="form-check">
                                        <input type="checkbox" name="addons[${addon.id}][id]"
                                               value="${addon.id}"
                                               class="form-check-input addon-checkbox"
                                               id="addon-${addon.id}">
                                        <input type="hidden" name="addons[${addon.id}][quantity]" value="1">
                                        <label class="form-check-label" for="addon-${addon.id}">
                                            <h6>${addon.name}</h6>
                                            <p class="text-muted small mb-2">${addon.description}</p>
                                            <span class="badge bg-primary">${addon.price} ريال</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    // إضافة مستمع أحداث للإضافات لتحديث سعر التقسيط
                    setTimeout(() => {
                        document.querySelectorAll('.addon-checkbox').forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                updateTotalPriceAndInstallment();
                            });
                        });
                    }, 100);

                    addonsSection.style.display = 'block';
                } else {
                    addonsSection.style.display = 'none';
                }

                // تحديث سعر التقسيط
                updateTotalPriceAndInstallment();

                // لن نطبق الكوبون تلقائيًا
                // ترك المستخدم ينسخ الكود ويستخدمه يدويًا
            }

            // Handle service selection
            serviceSelect.addEventListener('change', function() {
                const selectedServiceId = this.value;
                if (!selectedServiceId) {
                    packagesContainer.style.display = 'none';
                    addonsSection.style.display = 'none';
                    return;
                }

                // Remove any applied coupon when service is changed
                if (document.getElementById('coupon-details') && !document.getElementById('coupon-details').classList.contains('d-none')) {
                    removeCoupon();
                }

                // إلغاء تحديد أي باقة سابقة وإزالة التنسيق
                document.querySelectorAll('.package-select:checked').forEach(radio => radio.checked = false);
                document.querySelectorAll('.package-card.selected').forEach(card => card.classList.remove('selected'));

                // إلغاء تحديد الإضافات السابقة
                document.querySelectorAll('.addon-checkbox:checked').forEach(checkbox => {
                    checkbox.checked = false;
                });

                // إخفاء قسم التاريخ والوقت إذا كان موجودًا
                const dateTimeSection = document.getElementById('dateTimeSection');
                if (dateTimeSection) {
                    dateTimeSection.style.display = 'none';

                    // إعادة تعيين قيم حقول التاريخ والوقت
                    const dateInput = document.querySelector('input[name="session_date"]');
                    const timeSelect = document.getElementById('sessionTime');
                    if (dateInput) dateInput.value = '';
                    if (timeSelect) {
                        timeSelect.innerHTML = '<option value="">اختر الوقت</option>';
                        timeSelect.disabled = true;
                    }
                }

                // إخفاء قسم الإضافات حتى يتم اختيار باقة جديدة
                addonsSection.style.display = 'none';

                // Filter packages for selected service
                const servicePackages = allPackages.filter(pkg =>
                    pkg.service_ids.includes(parseInt(selectedServiceId))
                );

                // Update packages display with oldPackageId check
                packagesContainer.innerHTML = servicePackages.map(pkg => {
                    // إزالة التحديد التلقائي للباقة عند تغيير الخدمة
                    return `
                    <div class="col-md-6">
                        <div class="package-card">
                            <input type="radio" name="package_id" value="${pkg.id}"
                                   class="form-check-input package-select" required>
                            <h5>${pkg.name}</h5>
                            <p class="text-muted">${pkg.description}</p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-clock me-2"></i>المدة:
                                    <span class="duration-value">${pkg.duration}</span>
                                    ${pkg.duration >= 60
                                    ? `${Math.floor(pkg.duration / 60)} ساعة${pkg.duration % 60 > 0 ? ` و ${pkg.duration % 60} دقيقة` : ''}`
                                    : `${pkg.duration} دقيقة`
                                }</li>
                                <li><i class="fas fa-images me-2"></i>عدد الصور: ${pkg.num_photos}</li>
                                <li><i class="fas fa-palette me-2"></i>عدد الثيمات: ${pkg.themes_count}</li>
                                <li><i class="fas fa-tag me-2"></i>السعر: ${pkg.base_price} ريال</li>
                                @if(isset($package->best_coupon))
                                <li class="coupon-info">
                                    <i class="fas fa-ticket-alt me-2 coupon-icon"></i>
                                    كوبون خصم:
                                    <span class="coupon-code">{{ $package->best_coupon->code }}</span>
                                    <span class="discount-value">({{ $package->discount_text }})</span>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    `;
                }).join('');

                packagesContainer.style.display = 'flex';
                addonsSection.style.display = 'none';

                // Reattach package selection event listeners
                attachPackageListeners();
            });

            // تنفيذ اختيار الخدمة تلقائياً إذا كانت محددة مسبقاً
            if (serviceSelect.value) {
                // تشغيل حدث التغيير لعرض الباقات المناسبة
                serviceSelect.dispatchEvent(new Event('change'));
            }

            function attachPackageListeners() {
                document.querySelectorAll('.package-card').forEach(card => {
                    card.addEventListener('click', function() {
                        const radio = this.querySelector('input[type="radio"]');
                        if (radio) {
                            // إلغاء تحديد جميع الإضافات السابقة عند تغيير الباقة
                            document.querySelectorAll('.addon-checkbox:checked').forEach(checkbox => {
                                checkbox.checked = false;
                            });

                            radio.checked = true;
                            handlePackageSelection(radio.value);
                        }
                        document.querySelectorAll('.package-card').forEach(c => {
                            c.classList.remove('selected');
                        });
                        this.classList.add('selected');

                        // تحديث مبلغ التقسيط مباشرة عند اختيار باقة جديدة
                        updateTotalPriceAndInstallment();
                    });
                });
            }

            // دالة جديدة لحساب إجمالي السعر وتحديث التقسيط
            function updateTotalPriceAndInstallment() {
                const selectedPackageId = document.getElementById('package_id').value;
                if (!selectedPackageId) return;

                const selectedPackage = allPackages.find(pkg => pkg.id == selectedPackageId);
                if (!selectedPackage) return;

                // الحصول على أسعار الباقة
                let basePrice = parseFloat(selectedPackage.price);
                let totalPrice = basePrice;

                // إضافة أسعار الإضافات المحددة
                document.querySelectorAll('.addon-checkbox:checked').forEach(checkbox => {
                    const addonId = checkbox.value;
                    const addon = selectedPackage.addons.find(a => a.id == addonId);
                    if (addon) {
                        totalPrice += parseFloat(addon.price);
                    }
                });

                // لا نطبق الكوبون تلقائيًا هنا - فقط نعرض السعر الأصلي
                // سيتم تطبيق الكوبون فقط عندما يضيفه المستخدم يدويًا

                // تحديث السعر الإجمالي
                document.getElementById('totalPrice').textContent = totalPrice.toFixed(2) + ' ريال';

                // حساب التقسيط إذا كان متاحًا
                const installmentSection = document.getElementById('installment-section');
                if (selectedPackage.installment_available && installmentSection) {
                    const minDown = calculatePercentage(totalPrice, 20); // 20% دفعة أولى
                    const remainingAmount = totalPrice - minDown;
                    const monthlyInstallment = remainingAmount / 3; // التقسيط على 3 أشهر

                    document.getElementById('min-down-payment').textContent = minDown.toFixed(2) + ' ريال';
                    document.getElementById('monthly-installment').textContent = monthlyInstallment.toFixed(2) + ' ريال';
                    installmentSection.style.display = 'block';
                } else if (installmentSection) {
                    installmentSection.style.display = 'none';
                }

                return totalPrice;
            }

            // Check if a package is already selected (e.g. after form validation error)
            const selectedPackageRadio = document.querySelector('.package-select:checked');
            if (selectedPackageRadio) {
                handlePackageSelection(selectedPackageRadio.value);
                selectedPackageRadio.closest('.package-card').classList.add('selected');

                // If date is also selected, load the available time slots
                if (sessionDateInput.value) {
                    // Find the selected package to get its duration
                    const packageId = selectedPackageRadio.value;
                    const selectedPackage = allPackages.find(pkg => pkg.id == packageId);
                    if (selectedPackage) {
                        updateAvailableTimes(selectedPackage.duration);
                    }
                }
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

            // Coupon Code Handling
            const couponInput = document.getElementById('coupon_code');
            const checkCouponBtn = document.getElementById('check-coupon');
            const couponMessage = document.getElementById('coupon-message');
            const couponDetails = document.getElementById('coupon-details');
            const couponCodeDisplay = document.getElementById('coupon-code-display');
            const couponDiscountDisplay = document.getElementById('coupon-discount-display');
            const removeCouponBtn = document.getElementById('remove-coupon');

            if (checkCouponBtn && couponInput) {
                // Add input animation effect
                couponInput.addEventListener('focus', function() {
                    this.closest('.coupon-input-container').classList.add('focused');
                });

                couponInput.addEventListener('blur', function() {
                    this.closest('.coupon-input-container').classList.remove('focused');
                });

                // Handle enter key press
                couponInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && checkCouponBtn) {
                        e.preventDefault();
                        checkCouponBtn.click();
                    }
                });

                // Add button click animation
                checkCouponBtn.addEventListener('mousedown', function() {
                    this.classList.add('btn-pressed');
                });

                document.addEventListener('mouseup', function() {
                    checkCouponBtn.classList.remove('btn-pressed');
                });

                // Handle coupon verification
                checkCouponBtn.addEventListener('click', function() {
                    const couponCode = couponInput.value.trim();
                    if (!couponCode) {
                        showCouponMessage('يرجى إدخال كود الخصم', 'warning');
                        shakeCouponInput();
                        return;
                    }

                    const selectedPackageRadio = document.querySelector('.package-select:checked');
                    if (!selectedPackageRadio) {
                        showCouponMessage('يرجى اختيار باقة أولاً', 'warning');
                        return;
                    }

                    // Show loading state
                    showLoadingState(true);

                    const packageId = selectedPackageRadio.value;
                    validateCoupon(couponCode, packageId);
                });
            }

            if (removeCouponBtn) {
                removeCouponBtn.addEventListener('click', function() {
                    const appliedCoupon = this.closest('.applied-coupon');
                    appliedCoupon.classList.add('slide-out');

                    // Add slight delay before actually removing the coupon
                    setTimeout(() => {
                        removeCoupon();
                    }, 300);
                });
            }

            function showLoadingState(isLoading) {
                if (isLoading) {
                    checkCouponBtn.disabled = true;
                    checkCouponBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    couponInput.disabled = true;
                } else {
                    checkCouponBtn.disabled = false;
                    checkCouponBtn.innerHTML = '<span class="verify-text">تحقق</span><i class="fas fa-check"></i>';
                    couponInput.disabled = false;
                }
            }

            function shakeCouponInput() {
                const container = couponInput.closest('.coupon-input-container');
                container.classList.add('shake-animation');
                setTimeout(() => {
                    container.classList.remove('shake-animation');
                }, 500);
            }

            function validateCoupon(couponCode, packageId) {
                showCouponMessage('جاري التحقق من كود الخصم...', 'info');

                const tokenElement = document.querySelector('meta[name="csrf-token"]');
                if (!tokenElement) {
                    showLoadingState(false);
                    showCouponMessage('حدث خطأ في النظام. يرجى تحديث الصفحة والمحاولة مرة أخرى', 'danger');
                    return;
                }

                const token = tokenElement.getAttribute('content');

                fetch('/client/bookings/validate-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        coupon_code: couponCode,
                        package_id: packageId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    showLoadingState(false);

                    if (data.status === 'success') {
                        showCouponMessage('تم تطبيق كود الخصم بنجاح!', 'success');
                        setTimeout(() => {
                            couponMessage.innerHTML = '';
                            applyCoupon(data.coupon);
                        }, 1000);
                    } else {
                        showCouponMessage(data.message || 'كود الخصم غير صالح', 'danger');
                        shakeCouponInput();
                    }
                })
                .catch(error => {
                    console.error('Error validating coupon:', error);
                    showLoadingState(false);
                    showCouponMessage('حدث خطأ أثناء التحقق من كود الخصم', 'danger');
                });
            }

            function applyCoupon(coupon) {
                // Format coupon details
                couponCodeDisplay.textContent = coupon.code;

                let discountText = '';
                if (coupon.discount_type === 'percentage') {
                    discountText = `خصم ${coupon.discount_value}%`;
                } else {
                    discountText = `خصم ${coupon.discount_value} ريال`;
                }

                couponDiscountDisplay.textContent = discountText;

                // Animate coupon details appearing
                couponDetails.classList.remove('d-none');

                // Hide the input container with animation
                const couponInputContainer = document.querySelector('.coupon-input-container');
                couponInputContainer.style.opacity = '0.5';
                couponInputContainer.style.transform = 'scale(0.98)';

                // Update total calculation if needed
                updateTotalWithDiscount(coupon);
            }

            function removeCoupon() {
                // Reset input state
                const couponInputContainer = document.querySelector('.coupon-input-container');
                couponInput.value = '';
                couponInput.disabled = false;
                couponInputContainer.style.opacity = '1';
                couponInputContainer.style.transform = 'scale(1)';

                // Reset button state
                checkCouponBtn.disabled = false;
                checkCouponBtn.innerHTML = '<span class="verify-text">تحقق</span><i class="fas fa-check"></i>';

                // Hide coupon details section
                couponDetails.classList.add('d-none');

                // Clear any messages
                couponMessage.innerHTML = '';

                // Reset any price calculations
                updateTotalWithDiscount(null);

                // Add focus to input field
                setTimeout(() => {
                    couponInput.focus();
                }, 100);
            }

            function showCouponMessage(message, type) {
                // Create message element with icon
                const icon = type === 'info' ? 'info-circle' :
                            type === 'success' ? 'check-circle' :
                            type === 'warning' ? 'exclamation-triangle' :
                            'exclamation-circle';

                couponMessage.innerHTML = `
                    <div class="message-content text-${type}">
                        <i class="fas fa-${icon} me-2"></i>
                        <span>${message}</span>
                    </div>
                `;

                // Animate the message appearance
                const messageContent = couponMessage.querySelector('.message-content');
                if (messageContent) {
                    messageContent.style.opacity = '0';
                    messageContent.style.transform = 'translateY(10px)';

                    setTimeout(() => {
                        messageContent.style.transition = 'all 0.3s ease';
                        messageContent.style.opacity = '1';
                        messageContent.style.transform = 'translateY(0)';
                    }, 10);
                }
            }

            function updateTotalWithDiscount(coupon) {
                if (!coupon) {
                    document.getElementById('price-breakdown-section').style.display = 'none';
                    return;
                }

                // Get selected package price
                const selectedPackageRadio = document.querySelector('.package-select:checked');
                if (!selectedPackageRadio) return;

                const packageCard = selectedPackageRadio.closest('.package-card');
                const priceText = packageCard.querySelector('.fa-tag').parentElement.textContent;
                const originalPrice = parseFloat(priceText.match(/\d+(\.\d+)?/)[0]);

                // Calculate discount
                let discountAmount = 0;
                if (coupon.discount_type === 'percentage') {
                    discountAmount = (originalPrice * parseFloat(coupon.discount_value) / 100);
                } else {
                    discountAmount = parseFloat(coupon.discount_value);
                }

                // Make sure discount doesn't exceed the original price
                discountAmount = Math.min(discountAmount, originalPrice);

                // Calculate final price and savings percentage
                const finalPrice = originalPrice - discountAmount;
                const savingsPercentage = Math.round((discountAmount / originalPrice) * 100);

                // Update the price breakdown display
                document.getElementById('original-price-display').textContent = originalPrice.toFixed(2) + ' ريال';
                document.getElementById('discount-amount-display').textContent = discountAmount.toFixed(2) + ' ريال';
                document.getElementById('final-price-display').textContent = finalPrice.toFixed(2) + ' ريال';
                document.getElementById('savings-percentage').textContent = savingsPercentage + '%';

                // Add subtle animation to highlight the savings
                const savingsBadge = document.querySelector('.savings-badge');
                savingsBadge.classList.remove('pulsate-animation');
                void savingsBadge.offsetWidth; // Trigger reflow to restart animation
                savingsBadge.classList.add('pulsate-animation');

                // Show the price breakdown section with animation
                const priceSection = document.getElementById('price-breakdown-section');
                priceSection.style.display = 'block';
                priceSection.classList.remove('animate-slide-in');
                void priceSection.offsetWidth; // Trigger reflow to restart animation
                priceSection.classList.add('animate-slide-in');

                // Add selected addons to the total (if any)
                const addonCheckboxes = document.querySelectorAll('input[name^="addons"]:checked');
                    let addonsTotal = 0;
                if (addonCheckboxes.length > 0) {
                    addonCheckboxes.forEach(checkbox => {
                        const addonCard = checkbox.closest('.card');
                        const addonPriceText = addonCard.querySelector('.badge').textContent;
                        const addonPrice = parseFloat(addonPriceText.match(/\d+(\.\d+)?/)[0]);
                        addonsTotal += addonPrice;
                    });

                    // Add a row for addons if it doesn't exist
                    let addonRow = document.querySelector('.price-row.addons-row');
                    if (!addonRow) {
                        const priceBody = document.querySelector('.price-breakdown-body');
                        const totalRow = document.querySelector('.price-row.total-row');

                        addonRow = document.createElement('div');
                        addonRow.className = 'price-row addons-row';

                        const addonLabel = document.createElement('div');
                        addonLabel.className = 'price-label';
                        addonLabel.innerHTML = '<i class="fas fa-plus-circle me-2" style="color: #21B3B0;"></i>الإضافات';

                        const addonValue = document.createElement('div');
                        addonValue.className = 'price-value addons-value';
                        addonValue.id = 'addons-price-display';

                        addonRow.appendChild(addonLabel);
                        addonRow.appendChild(addonValue);

                        priceBody.insertBefore(addonRow, totalRow);
                    }

                    // Update addons price
                    document.getElementById('addons-price-display').textContent = addonsTotal.toFixed(2) + ' ريال';

                    // Update final price to include addons
                    const totalWithAddons = finalPrice + addonsTotal;
                    document.getElementById('final-price-display').textContent = totalWithAddons.toFixed(2) + ' ريال';

                    // تحديث مبلغ التقسيط
                    const tabbyInstallmentText = document.querySelector('.tabby-installment-amount');
                    if (tabbyInstallmentText) {
                        const installmentAmount = (totalWithAddons / 4).toFixed(2);
                        tabbyInstallmentText.textContent = installmentAmount;
                    }

                    // تحديث widget تابي
                    const tabbyRadio = document.getElementById('payment_tabby');
                    if (tabbyRadio && tabbyRadio.checked) {
                        updateTabbyWidgets(totalWithAddons);
                    }
                } else {
                    // Remove addons row if no addons are selected
                    const addonRow = document.querySelector('.price-row.addons-row');
                    if (addonRow) {
                        addonRow.remove();
                    }

                    // تحديث مبلغ التقسيط
                    const tabbyInstallmentText = document.querySelector('.tabby-installment-amount');
                    if (tabbyInstallmentText) {
                        const installmentAmount = (finalPrice / 4).toFixed(2);
                        tabbyInstallmentText.textContent = installmentAmount;
                    }

                    // تحديث widget تابي
                    const tabbyRadio = document.getElementById('payment_tabby');
                    if (tabbyRadio && tabbyRadio.checked) {
                        updateTabbyWidgets(finalPrice);
                    }
                }
            }

            // Payment method handling
            const tabbyRadio = document.getElementById('payment_tabby');
            const codRadio = document.getElementById('payment_cod');
            const submitBtn = document.getElementById('submitBtn');
            const tabbyContainer = document.getElementById('tabby-container');

            // Update button text based on payment method
            function updateButtonText() {
                if (tabbyRadio && codRadio) {
                    if (tabbyRadio.checked) {
                        submitBtn.innerHTML = '<i class="fas fa-shopping-bag me-2"></i> متابعة للدفع مع تابي';
                        tabbyContainer.style.display = 'block';

                        // تحديث مبلغ التقسيط عند اختيار الدفع بتابي
                        const selectedPackageRadio = document.querySelector('.package-select:checked');
                        if (selectedPackageRadio) {
                            const packageCard = selectedPackageRadio.closest('.package-card');
                            const priceText = packageCard.querySelector('.fa-tag').parentElement.textContent;
                            const packagePrice = parseFloat(priceText.match(/\d+(\.\d+)?/)[0]);
                            updateTabbyWidgets(packagePrice);
                        }
                    } else if (codRadio.checked) {
                        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i> تأكيد الحجز';
                        tabbyContainer.style.display = 'none';
                    }
                }
            }

            // Initialize button text if payment methods exist
            if (tabbyRadio && codRadio) {
                updateButtonText();

                // Update button text when payment method changes
                tabbyRadio.addEventListener('change', function() {
                    updateButtonText();
                    // تحديث مباشر لمبلغ التقسيط عند اختيار طريقة الدفع بتابي
                    updateTotalPriceAndInstallment();
                });

                // إضافة مستمع لخيار الدفع عند الاستلام
                codRadio.addEventListener('change', updateButtonText);
            }

            // Update Tabby widgets when package is selected
            function updateTabbyWidgets(price) {
                if (typeof TabbyPromo === 'undefined') {
                    console.error('TabbyPromo is not defined');
                    return;
                }

                // تحديث العنصر الخاص بعرض التقسيط
                try {
                // Tabby Product Widget
                new TabbyPromo({
                    selector: '#tabby-product-widget',
                    currency: 'SAR',
                    price: price.toString(),
                    lang: 'ar',
                    source: 'checkout'
                });

                    // تحديث مبلغ التقسيط في النص
                    const installmentAmount = (price / 4).toFixed(2);
                    const tabbyInstallmentText = document.querySelector('.tabby-installment-amount');
                    if (tabbyInstallmentText) {
                        tabbyInstallmentText.textContent = installmentAmount;
                    }
                } catch (error) {
                    console.error('Error updating Tabby widget:', error);
                }
            }

            // Update package price for Tabby when a package is selected
            function updatePackagePrice() {
                updateTotalPriceAndInstallment();
            }

            // Add event listener to package selection
            document.querySelectorAll('.package-select').forEach(radio => {
                radio.addEventListener('change', updatePackagePrice);
            });

            // Initialize Tabby widget if a package is already selected
            const initialSelectedPackage = document.querySelector('.package-select:checked');
            if (initialSelectedPackage) {
                updatePackagePrice();

                // تهيئة مبلغ التقسيط إذا كان هناك باقة محددة بالفعل
                updateTotalPriceAndInstallment();
            }

            // Update Tabby widget when coupon is applied
            const oldApplyCoupon = window.applyCoupon;
            if (typeof oldApplyCoupon === 'function') {
                window.applyCoupon = function(data) {
                    // Call the original function
                    oldApplyCoupon(data);

                    // Update Tabby widget with new price after discount
                    const finalPriceElement = document.getElementById('final-price-display');
                    if (finalPriceElement) {
                        const finalPriceText = finalPriceElement.textContent;
                        const finalPrice = parseFloat(finalPriceText.match(/\d+(\.\d+)?/)[0]);
                        updateTabbyWidgets(finalPrice);
                    }
                };
            }

            // Handle form submission
            const checkoutForm = document.getElementById('checkout-form');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function() {
                    this.classList.add('loading');
                });
            }

            // Add event listeners to package selection
            document.querySelectorAll('.package-card').forEach(card => {
                const radio = card.querySelector('.package-select');
                if (radio) {
                    card.addEventListener('click', function() {
                        const radio = this.querySelector('.package-select');
                        if (radio) {
                            radio.checked = true;
                            handlePackageSelection(radio.value);
                        }
                        document.querySelectorAll('.package-card').forEach(c => {
                            c.classList.remove('selected');
                        });
                        this.classList.add('selected');

                        // تحديث مبلغ التقسيط مباشرة عند اختيار باقة جديدة
                        updateTotalPriceAndInstallment();
                    });
                }
            });
        });
    </script>
</body>
</html>
