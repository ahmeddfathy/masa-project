<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="تعرف على عدسة سوما - استوديو تصوير متخصص في تصوير العائلات والأطفال في ابها حي المحالة. نقدم خدمات تصوير المواليد والأطفال بجودة عالية وتقنيات متطورة.">
    <meta name="keywords" content="عدسة سوما، استوديو تصوير، تصوير عائلي، تصوير أطفال، تصوير مواليد، استوديو في ابها، حي المحالة، خدمات تصوير">
    <meta name="author" content="عدسة سوما">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">

    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="عدسة سوما">
    <meta property="og:title" content="من نحن - عدسة سوما | استوديو تصوير متخصص في ابها حي المحالة">
    <meta property="og:description" content="تعرف على عدسة سوما - استوديو تصوير متخصص في تصوير العائلات والأطفال في ابها حي المحالة. نقدم خدمات تصوير المواليد والأطفال بجودة عالية وتقنيات متطورة.">
    <meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="من نحن - عدسة سوما | استوديو تصوير متخصص في ابها حي المحالة">
    <meta name="twitter:description" content="تعرف على عدسة سوما - استوديو تصوير متخصص في تصوير العائلات والأطفال في ابها حي المحالة. نقدم خدمات تصوير المواليد والأطفال بجودة عالية وتقنيات متطورة.">
    <meta name="twitter:image" content="{{ asset('assets/images/logo.png') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <title>من نحن - عدسة سوما | استوديو تصوير متخصص في ابها حي المحالة</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/style.css') }}?t={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/about.css') }}?t={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/responsive.css') }}?t={{ time() }}">

</head>
<body>
    @include('parts.navbar')

    <!-- Page Header -->
    <section class="page-header" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), url('assets/images/about/1.jpg') no-repeat center center; background-size: cover; padding: 150px 0; color: white; text-align: center;" >
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
                        <h2>نبذة عنا</h2>
                        <p>سمية الشهري، أم لأجمل طفلتين، مؤسسة أول استديو احترافي متخصص في تصوير المواليد والنساء والحوامل وتصوير المستشفى والأطفال على مستوى المنطقة الجنوبية.</p>
                        <p>تأسس الاستديو في عام ٢٠١٨ في مدينة أبها، حيث تقوم الفوتغرافية سمية الشهري بالتصوير بنفسها مع فريق العمل المتخصص.</p>
                        <p>نحرص على تقديم تجربة تصوير فريدة لكل عائلة تزورنا، ونسعى لتخليد ذكرياتهم الثمينة للأبد بأسلوب مميز واحترافي.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/about/23.jpg" alt="استوديو التصوير" class="img-fluid" loading="lazy">
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

    @include('parts.footer')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

