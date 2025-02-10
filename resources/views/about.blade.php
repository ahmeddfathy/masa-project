<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>من نحن - عدسة سوما</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/about.css') }}">
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

    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1542038784456-1ea8e935640e') no-repeat center center; background-size: cover; padding: 150px 0; color: white; text-align: center;">
        <div class="container">
            <h1 style="font-size: 3.5rem; font-weight: 700; margin-bottom: 1.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">من نحن</h1>
            <p style="font-size: 1.25rem; max-width: 800px; margin: 0 auto; line-height: 1.8; text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">عدسة سوما - استوديو تصوير مختص بالعائلة والأطفال في المملكة العربية السعودية</p>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="our-story">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="content-wrapper">
                        <h2>قصتنا</h2>
                        <p>نحن استوديو تصوير مختص تحت مسمى "عدسة سوما" في أحد مدن المملكة العربية السعودية. يقوم الاستوديو بتقديم خدمات التصوير الفوتوغرافي للأطفال والعائلات.</p>
                        <p>نقدم خدماتنا للفئة الأولى من الأطفال من الولادة إلى 14 يوم، والفئة الثانية للأطفال من عمر 3 أشهر إلى 5 أشهر. في الفئة الثالثة نقدم خدمات مؤسسة فاخرة وإلكترونية للميلاد والولادة في الموقع المشروع.</p>
                        <p>نتميز بتقديم تجربة فريدة وتكنولوجيا متقدمة لتحقيق أفضل النتائج، ونسعى للجمع والاطلاع على الجديد وتوسيع المشروع ليصبح سلسلة متكاملة.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e" alt="استوديو التصوير" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="text-center mb-5">فريقنا المحترف</h2>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="team-member">
                        <div class="member-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e" alt="مصور محترف">
                        </div>
                        <h4>مصور محترف</h4>
                        <div class="position">مصور رئيسي</div>
                        <p>متخصص في تصوير الأطفال والعائلات</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="team-member">
                        <div class="member-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330" alt="مصورة محترفة">
                        </div>
                        <h4>مصورة محترفة</h4>
                        <div class="position">مصورة متخصصة</div>
                        <p>متخصصة في تصوير المواليد والأطفال</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="team-member">
                        <div class="member-img-wrapper">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d" alt="مصور فيديو">
                        </div>
                        <h4>مصور فيديو</h4>
                        <div class="position">مصور متخصص</div>
                        <p>متخصص في التصوير والمونتاج للمناسبات العائلية</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Achievements -->
    <section class="achievements">
        <div class="container">
            <h2 class="text-center mb-5">إنجازاتنا</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="achievement-card">
                        <i class="fas fa-camera"></i>
                        <h4>1000+</h4>
                        <p>جلسة تصوير</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="achievement-card">
                        <i class="fas fa-heart"></i>
                        <h4>500+</h4>
                        <p>عميل سعيد</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="achievement-card">
                        <i class="fas fa-award"></i>
                        <h4>15+</h4>
                        <p>جائزة</p>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="achievement-card">
                        <i class="fas fa-calendar"></i>
                        <h4>8</h4>
                        <p>سنوات خبرة</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="our-values">
        <div class="container">
            <h2 class="text-center mb-5">قيمنا</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="value-card">
                        <i class="fas fa-star"></i>
                        <h4>الجودة</h4>
                        <p>الالتزام بأعلى معايير الجودة في جميع جوانب العمل من التصوير وتقديم الخدمات</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-card">
                        <i class="fas fa-heart"></i>
                        <h4>الابتكار</h4>
                        <p>السعي المستمر لتقديم خدمات جديدة ومبتكرة للتميز عن المنافسين</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-card">
                        <i class="fas fa-handshake"></i>
                        <h4>الاحترافية</h4>
                        <p>الشفافية والحفاظ على الثقة في جميع التعاملات مع العملاء</p>
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

