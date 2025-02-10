<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد | المتجر الحديث</title>
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/signup.css') }}">
</head>
<body>
    <div class="signup-container">
        <!-- Decorative Elements -->
        <div class="decorative-circle circle-1"></div>
        <div class="decorative-circle circle-2"></div>

        <!-- Signup Form -->
        <div class="signup-form-container">
            <div class="wave-decoration"></div>
            <div class="form-card">
                <h1 class="signup-title">إنشاء حساب جديد</h1>
                <p class="text-muted mb-4">أدخل بياناتك لإنشاء حساب جديد</p>

                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}" class="signup-form">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">الاسم الكامل</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" name="name" :value="old('name')" placeholder="أدخل اسمك الكامل" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" name="email" :value="old('email')" placeholder="example@domain.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">رقم الهاتف</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" class="form-control" name="phone" :value="old('phone')" placeholder="05xxxxxxxx" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">العنوان</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" class="form-control" name="address" :value="old('address')" placeholder="أدخل عنوانك" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="password" placeholder="********" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="password_confirmation" placeholder="********" required>
                        </div>
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                        <label class="form-check-label" for="terms">
                            {!! __('أوافق على :terms_of_service و :privacy_policy', [
                                'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-primary">'.__('الشروط والأحكام').'</a>',
                                'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-primary">'.__('سياسة الخصوصية').'</a>',
                            ]) !!}
                        </label>
                    </div>
                    @endif

                    <button type="submit" class="btn-signup">
                        <i class="fas fa-user-plus me-2"></i>
                        إنشاء الحساب
                    </button>

                    <p class="login-link mt-4">
                        لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/signup.js') }}"></script>
</body>
</html>
