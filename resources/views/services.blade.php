<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خدماتنا - عدسة سوما</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/services.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/responsive.css') }}">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">عدسة سوما</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">معرض الصور</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">خدماتنا</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">منتجاتنا</a>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">التسجيل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">تسجيل الدخول</a>
                        </li>
                    @endguest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">من نحن</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">اتصل بنا</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">لوحة التحكم</a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="nav-link" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();">
                                    تسجيل الخروج
                                </a>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>


    <!-- Services Header -->
    <section class="hero-section">
        <div class="hero-image" style="background-image: url('https://images.unsplash.com/photo-1533483595632-c5f0e57a1936?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');"></div>
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <h1 class="text-center text-white mb-3">خدماتنا</h1>
                <p class="text-center text-white mb-0">يقدم الاستوديو خدماته للعائلات والأطفال، وتتمثل في:</p>
            </div>
        </div>
    </section>

    <!-- Main Services -->
    <section class="services-details">
        <div class="container">
            <div class="row g-4">
                <!-- التصوير الفوتوغرافي -->
                <div class="col-lg-6">
                    <div class="service-detail">
                        <div class="service-image">
                            <img src="https://images.unsplash.com/photo-1581952976147-5a2d15560349?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80" alt="تصوير الأطفال">
                        </div>
                        <div class="icon-wrapper">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h3>التصوير الفوتوغرافي</h3>
                        <p>خدمات تصوير فوتوغرافي عالية الجودة للأطفال والعائلات</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i>تصوير المواليد من الولادة إلى 14 يوم</li>
                            <li><i class="fas fa-check-circle"></i>تصوير الأطفال من عمر 3 أشهر إلى 5 أشهر</li>
                            <li><i class="fas fa-check-circle"></i>جلسات تصوير عائلية</li>
                            <li><i class="fas fa-check-circle"></i>معالجة احترافية للصور</li>
                        </ul>
                    </div>
                </div>

                <!-- عمل مجسمات ثري دي -->
                <div class="col-lg-6">
                    <div class="service-detail">
                        <div class="service-image">
                            <img src="https://images.unsplash.com/photo-1535572290543-960a8046f5af?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="مجسمات ثري دي">
                        </div>
                        <div class="icon-wrapper">
                            <i class="fas fa-cube"></i>
                        </div>
                        <h3>عمل مجسمات ثري دي</h3>
                        <p>تصميم وتنفيذ مجسمات ثري دي للذكريات العائلية</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i>مجسمات ثري دي للصور</li>
                            <li><i class="fas fa-check-circle"></i>تصميمات مبتكرة وفريدة</li>
                            <li><i class="fas fa-check-circle"></i>جودة عالية في التنفيذ</li>
                            <li><i class="fas fa-check-circle"></i>خيارات متعددة للتصميم</li>
                        </ul>
                    </div>
                </div>

                <!-- الصور المطبوعة والفاخرة -->
                <div class="col-lg-6">
                    <div class="service-detail">
                        <div class="service-image">
                            <img src="https://images.unsplash.com/photo-1506619216599-9d16d0903dfd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1469&q=80" alt="صور مطبوعة">
                        </div>
                        <div class="icon-wrapper">
                            <i class="fas fa-images"></i>
                        </div>
                        <h3>الصور المطبوعة والفاخرة</h3>
                        <p>طباعة الصور بجودة عالية وخيارات فاخرة</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i>طباعة بأعلى جودة</li>
                            <li><i class="fas fa-check-circle"></i>خيارات متعددة للطباعة</li>
                            <li><i class="fas fa-check-circle"></i>ألبومات فاخرة</li>
                            <li><i class="fas fa-check-circle"></i>تغليف وتقديم مميز</li>
                        </ul>
                    </div>
                </div>

                <!-- البومات الأطفال -->
                <div class="col-lg-6">
                    <div class="service-detail">
                        <div class="service-image">
                            <img src="https://images.unsplash.com/photo-1472162072942-cd5147eb3902?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1469&q=80" alt="البومات الأطفال">
                        </div>
                        <div class="icon-wrapper">
                            <i class="fas fa-book"></i>
                        </div>
                        <h3>البومات الأطفال</h3>
                        <p>ألبومات مخصصة لتوثيق ذكريات الأطفال</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i>تصميمات خاصة للأطفال</li>
                            <li><i class="fas fa-check-circle"></i>جودة عالية في الطباعة</li>
                            <li><i class="fas fa-check-circle"></i>خيارات متنوعة للألبومات</li>
                            <li><i class="fas fa-check-circle"></i>تخصيص كامل حسب الطلب</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us with Background -->
    <section class="why-choose-us curved-bottom" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1554048612-b6a482bc67e5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80') no-repeat center center fixed; background-size: cover;">
        <div class="container">
            <h2 class="text-center mb-5 text-white display-4 fw-bold">لماذا تختارنا؟</h2>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card glass-effect hover-lift">
                        <i class="fas fa-award fa-3x mb-4"></i>
                        <h4 class="fw-bold">جودة استثنائية</h4>
                        <p class="text-white-50">نلتزم بتقديم أعلى معايير الجودة في كل تفاصيل عملنا</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card glass-effect hover-lift">
                        <i class="fas fa-camera-retro fa-3x mb-4"></i>
                        <h4 class="fw-bold">تقنيات متطورة</h4>
                        <p class="text-white-50">نستخدم أحدث التقنيات العالمية في التصوير والمعالجة</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card glass-effect hover-lift">
                        <i class="fas fa-heart fa-3x mb-4"></i>
                        <h4 class="fw-bold">خدمة مميزة</h4>
                        <p class="text-white-50">نقدم تجربة فريدة تفوق توقعاتكم في كل مرة</p>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3>عدسة سوما</h3>
                    <p>نوثق لحظاتكم الجميلة بلمسة فنية مميزة</p>
                </div>
                <div class="col-md-4">
                    <h3>تواصل معنا</h3>
                    <p>
                        <i class="fas fa-phone"></i> +966 50 000 0000<br>
                        <i class="fas fa-envelope"></i> info@studio.com
                    </p>
                </div>
                <div class="col-md-4">
                    <h3>تابعنا على</h3>
                    <div class="social-links">
                        <a href="#" class="me-2"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="me-2"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p>&copy; 2024 عدسة سوما. جميع الحقوق محفوظة</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
