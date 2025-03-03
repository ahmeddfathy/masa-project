<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="استوديو عدسة سوما - استوديو تصوير احترافي للعائلات والأطفال في ابها حي المحالة. نقدم خدمات التصوير الفوتوغرافي، مجسمات ثري دي، والبومات مطبوعة بجودة عالية.">
    <meta name="keywords" content="استوديو تصوير، تصوير عائلي، تصوير أطفال، استوديو في ابها، حي المحالة، عدسة سوما، البومات صور، مجسمات ثري دي، تصوير مناسبات، تصوير احترافي">
    <meta name="author" content="عدسة سوما">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">

    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="عدسة سوما">
    <meta property="og:title" content="عدسة سوما - استوديو التصوير العائلي في ابها حي المحالة">
    <meta property="og:description" content="استوديو تصوير احترافي للعائلات والأطفال في ابها حي المحالة. نقدم خدمات التصوير الفوتوغرافي، مجسمات ثري دي، والبومات مطبوعة بجودة عالية.">
    <meta property="og:image" content="/assets/images/logo.png" loading="lazy">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="عدسة سوما - استوديو التصوير العائلي في ابها حي المحالة">
    <meta name="twitter:description" content="استوديو تصوير احترافي للعائلات والأطفال في ابها حي المحالة. نقدم خدمات التصوير الفوتوغرافي، مجسمات ثري دي، والبومات مطبوعة بجودة عالية.">
    <meta name="twitter:image" content="/assets/images/logo.png">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <title>عدسة سوما - استوديو التصوير العائلي في ابها حي المحالة | تصوير احترافي للعائلات والأطفال</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Lightbox CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/studio-client/style.css">
    <link rel="stylesheet" href="/assets/css/studio-client/index.css">
    <link rel="stylesheet" href="/assets/css/studio-client/responsive.css">
    <style>
        /* تحسين الناف بار في الموبايل */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: rgba(33, 179, 176, 0.95) !important;
                padding: 1rem;
                border-radius: 15px;
                margin-top: 1rem;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                max-height: 80vh;
                overflow-y: auto;
            }

            .navbar-nav .nav-item {
                margin: 0.5rem 0;
            }

            .navbar-nav .nav-link {
                padding: 0.8rem 1.2rem !important;
                border-radius: 10px;
                transition: all 0.3s ease;
                font-size: 1.1rem;
                font-weight: 500;
                color: white !important;
                background: rgba(255, 255, 255, 0.1);
                margin: 0.3rem 0;
            }

            .navbar-nav .nav-link:hover,
            .navbar-nav .nav-link.active {
                background: rgba(255, 255, 255, 0.2);
                transform: translateX(-5px);
            }

            .navbar-toggler {
                border: none !important;
                padding: 0.6rem;
                font-size: 1.2rem;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 10px;
                color: white;
            }

            .navbar-toggler:focus {
                box-shadow: none;
                outline: none;
            }
        }
    </style>
</head>
<body>
    @include('parts.navbar')

    <!-- Hero Section -->
    <section class="carousel-section">
        <div id="servicesCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#servicesCarousel" data-bs-slide-to="3"></button>
            </div>
            <div class="carousel-inner">
                <!-- التصوير الفوتوغرافي -->
                <div class="carousel-item active">
                    <div class="carousel-image" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/images/home/30.jpg');">
                        <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                            <div class="caption-content">
                                <h2 class="display-4 fw-bold mb-3">التصوير الفوتوغرافي</h2>
                                <p class="lead mb-4">خدمات تصوير احترافية للعائلات والأطفال</p>
                                <a href="{{ route('services') }}" class="btn btn-primary btn-lg">اكتشف المزيد</a>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- مجسمات ثري دي -->
                <div class="carousel-item">
                    <div class="carousel-image" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/images/home/3D.jpg');">
                        <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                            <div class="caption-content">
                                <h2 class="display-4 fw-bold mb-3">مجسمات ثري دي</h2>
                                <p class="lead mb-4">تصميم وتنفيذ مجسمات ثري دي للذكريات العائلية</p>
                                                       <a href="{{ route('services') }}" class="btn btn-primary btn-lg">اكتشف المزيد</a>
>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الصور المطبوعة -->
                <div class="carousel-item">
                    <div class="carousel-image" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/images/home/33.jpg');">
                        <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                            <div class="caption-content">
                                <h2 class="display-4 fw-bold mb-3">الصور المطبوعة الفاخرة</h2>
                                <p class="lead mb-4">طباعة الصور بجودة عالية وخيارات متعددة</p>
                                                           <a href="{{ route('services') }}" class="btn btn-primary btn-lg">اكتشف المزيد</a>
>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- البومات الأطفال -->
                <div class="carousel-item">
                    <div class="carousel-image" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/images/home/15.jpg');">
                        <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                            <div class="caption-content">
                                <h2 class="display-4 fw-bold mb-3">البومات الأطفال</h2>
                                <p class="lead mb-4">ألبومات مخصصة لتوثيق ذكريات الأطفال</p>
                                                             <a href="{{ route('services') }}" class="btn btn-primary btn-lg">اكتشف المزيد</a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#servicesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">السابق</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#servicesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">التالي</span>
            </button>
        </div>
    </section>

    <!-- Featured Services -->
    <section class="services section-padding curved-top curved-bottom">
        <div class="container">
            <h2 class="text-center mb-5" style="font-weight: 500;">خدماتنا المميزة</h2>
            <div class="row">
                @foreach($services as $service)
                    <div class="col-md-4">
                        <div class="service-card glass-card">
                            <i class="fas fa-camera"></i>
                            <h3 style="color: black;">{{ $service->name }}</h3>
                            <p style="color: black;">{{ $service->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="wave-decoration">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M985.66,92.83C906.67,72,823.78,31,743.84,14.19c-82.26-17.34-168.06-16.33-250.45.39-57.84,11.73-114,31.07-172,41.86A600.21,600.21,0,0,1,0,27.35V120H1200V95.8C1132.19,118.92,1055.71,111.31,985.66,92.83Z" class="shape-fill"></path>
            </svg>
        </div>
    </section>

    <!-- Latest Work -->
    <section class="gallery section-padding curved-top">
        <div class="container">
            <h2 class="text-center mb-5" style="font-weight: 500; position: relative; z-index: 1;">أحدث أعمالنا</h2>
            <div class="row g-4">
                @foreach($latestImages as $image)
                    <div class="col-md-4">
                        <div class="gallery-item glass-effect">
                            <img src="{{ url('storage/' . $image->image_url) }}" alt="{{ $image->caption }}" class="img-fluid" loading="lazy">
                            <div class="gallery-overlay">
                                <div class="gallery-info">
                                    <h4>{{ $image->caption }}</h4>
                                    <p>{{ $image->category }}</p>
                                    <a href="{{ url('storage/' . $image->image_url) }}" data-lightbox="gallery" class="gallery-icon">
                                        <i class="fas fa-expand"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('gallery') }}" class="btn btn-primary">شاهد المزيد من أعمالنا</a>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section class="about-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-content">
                        <h2 class="section-title" style="font-weight: 500;">من نحن</h2>
                        <p class="section-description">
                            نحن في عدسة سوما نقدم خدمات التصوير الفوتوغرافي للعائلات والأطفال مع خدمات متكاملة تشمل مجسمات الليزر والصور المغناطيسية والمؤطرة. نسعى دائماً لتقديم أعلى مستويات الجودة والإبداع في كل صورة نلتقطها.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-image">
                        <img src="assets/images/home/30.jpg" alt="استوديو سوما" class="img-fluid rounded-3 shadow" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-section py-5" id="contact">
        <div class="container">
            <h2 class="text-center mb-5" style="font-weight: 500;">تواصل معنا</h2>
            <div class="row">
                <!-- Contact Info Side -->
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="contact-info-side">
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-info-content">
                                <h4>موقعنا</h4>
                                <p>ابها - حي المحالة</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-info-content">
                                <h4>اتصل بنا</h4>
                                <p>0561667885</p>

                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-info-content">
                                <h4>ساعات العمل</h4>
                                <p>السبت - الخميس</p>
                                <p>10:00 صباحاً - 6:00 مساءً</p>
                                <p>مواعيد رمضان: 8:30 مساءً - 2:00 صباحاً</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-info-content">
                                <h4>البريد الإلكتروني</h4>
                                <p>lens_soma@outlook.sa
</p>

                            </div>
                        </div>

                        <div class="social-links-contact">
                            <a href="https://wa.me/0561667885" target="_blank" title="واتساب"><i class="fab fa-whatsapp"></i></a>
                            <a href="/https://www.instagram.com/lens_soma_studio/?igsh=d2ZvaHZqM2VoMWsw#" target="_blank" title="انستغرام"><i class="fab fa-instagram"></i></a>
                         </a>
                        </div>
                    </div>
                </div>

                <!-- Contact Form Side -->
                <div class="col-lg-8">
                    <div class="contact-form-side">
                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('contact.send') }}" method="POST" class="contact-form">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="form-label">الاسم</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required placeholder="أدخل اسمك الكامل">
                                @error('name')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required placeholder="أدخل بريدك الإلكتروني">
                                @error('email')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">رقم الجوال</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}" required placeholder="أدخل رقم جوالك">
                                @error('phone')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="message" class="form-label">الرسالة</label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          id="message" name="message" rows="5" required placeholder="اكتب رسالتك هنا...">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>
                                إرسال الرسالة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('parts.footer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        // Initialize Lightbox
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'showImageNumberLabel': false
        });
    </script>
    <!-- Add before the footer -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.1/jquery.waypoints.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js"></script>
    <script>

    </script>
</body>
</html>
