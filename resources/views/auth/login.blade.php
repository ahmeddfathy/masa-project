<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | المتجر الحديث</title>
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

        <!-- Login Form -->
        <div class="signup-form-container">
            <div class="wave-decoration"></div>
            <div class="form-card">
                <h1 class="signup-title">تسجيل الدخول</h1>
                <p class="text-muted mb-4">أدخل بيانات حسابك</p>

        <x-validation-errors class="mb-4" />

        @session('status')
                    <div class="alert alert-success">
                {{ $value }}
            </div>
        @endsession

                <form method="POST" action="{{ route('login') }}" class="signup-form">
            @csrf

                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control" name="email" :value="old('email')" placeholder="example@domain.com" required autofocus>
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

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                        <label class="form-check-label" for="remember_me">
                            تذكرني
                </label>
            </div>

                    <button type="submit" class="btn-signup">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        تسجيل الدخول
                    </button>

                @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a href="{{ route('password.request') }}" class="forgot-password">
                                نسيت كلمة المرور؟
                    </a>
                        </div>
                @endif

                    <p class="login-link mt-4">
                        ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
