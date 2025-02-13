<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معرض الصور - عدسة سوما</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Lightbox CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/gallery.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/responsive.css') }}">
</head>
<body>
    @include('parts.navbar')

    <!-- Gallery Carousel -->
    <section class="gallery-carousel-section">
        <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
            <!-- Carousel Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="2"></button>
                <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="3"></button>
                <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="4"></button>
            </div>

            <!-- Carousel Items -->
            <div class="carousel-inner">
                <!-- Outdoor Photo -->
                <div class="carousel-item active">
                    <div class="carousel-image-container">
                        <img src="https://images.unsplash.com/photo-1533227268428-f9ed0900fb3b" class="d-block w-100" alt="خروجات وأنشطة" loading="lazy">
                        <div class="carousel-caption">
                            <h3>خروجات وأنشطة</h3>
                            <p>توثيق المغامرات والذكريات الجميلة</p>
                        </div>
                    </div>
                </div>

                <!-- Family Photo -->
                <div class="carousel-item">
                    <div class="carousel-image-container">
                        <img src="https://images.unsplash.com/photo-1609220136736-443140cffec6" class="d-block w-100" alt="جلسة عائلية" loading="lazy">
                        <div class="carousel-caption">
                            <h3>لحظات عائلية</h3>
                            <p>ذكريات لا تنسى</p>
                        </div>
                    </div>
                </div>

                <!-- Portrait Photo -->
                <div class="carousel-item">
                    <div class="carousel-image-container">
                        <img src="https://images.unsplash.com/photo-1623091411395-09e79fdbfcf3" class="d-block w-100" alt="بورتريه شخصي" loading="lazy">
                        <div class="carousel-caption">
                            <h3>بورتريه احترافي</h3>
                            <p>صور شخصية مميزة</p>
                        </div>
                    </div>
                </div>

                <!-- Birthday Photo -->
                <div class="carousel-item">
                    <div class="carousel-image-container">
                        <img src="https://images.unsplash.com/photo-1527529482837-4698179dc6ce" class="d-block w-100" alt="حفل عيد ميلاد" loading="lazy">
                        <div class="carousel-caption">
                            <h3>أعياد ميلاد</h3>
                            <p>لحظات الفرح والسعادة</p>
                        </div>
                    </div>
                </div>

                <!-- Children Photo -->
                <div class="carousel-item">
                    <div class="carousel-image-container">
                        <img src="https://images.unsplash.com/photo-1516627145497-ae6968895b74" class="d-block w-100" alt="تصوير أطفال" loading="lazy">
                        <div class="carousel-caption">
                            <h3>لحظات الطفولة</h3>
                            <p>ابتسامات بريئة</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 1rem;"></span>
                <span class="visually-hidden">السابق</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 1rem;"></span>
                <span class="visually-hidden">التالي</span>
            </button>
        </div>
    </section>

    <!-- Gallery Categories -->
    <section class="gallery-filter">
        <div class="container">
            <div class="text-center">
                <button class="active" data-filter="all">جميع الصور</button>
                @foreach($categories as $category)
                    <button data-filter="{{ $category }}">{{ $category }}</button>
                @endforeach
                </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="gallery">
        <div class="container">
            <div class="row" id="gallery-grid">
                @foreach($images as $category => $categoryImages)
                    @foreach($categoryImages as $image)
                        <div class="col-md-4" data-category="{{ $category }}">
                    <div class="gallery-item">
                                <img src="{{ Storage::url($image->image_url) }}" alt="{{ $image->caption }}" loading="lazy">
                        <div class="gallery-overlay">
                            <div class="gallery-info">
                                        <h4>{{ $image->caption }}</h4>
                                        <p>{{ $category }}</p>
                                        <a href="{{ Storage::url($image->image_url) }}" data-lightbox="gallery" class="gallery-icon">
                                    <i class="fas fa-expand"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                            </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>

    @include('parts.footer')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <!-- Gallery Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter functionality
            const filterButtons = document.querySelectorAll('.gallery-filter button');
            const galleryItems = document.querySelectorAll('#gallery-grid .col-md-4');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');

                    const filterValue = this.getAttribute('data-filter');

                    galleryItems.forEach(item => {
                        const itemCategory = item.getAttribute('data-category');

                        // Reset animation
                        item.style.animation = 'none';
                        item.offsetHeight; // Trigger reflow

                        if (filterValue === 'all' || filterValue === itemCategory) {
                            item.style.display = 'block';
                            item.style.animation = 'fadeIn 0.6s ease forwards';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

            // Initialize Lightbox with custom options
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'showImageNumberLabel': false,
                'fadeDuration': 300,
                'imageFadeDuration': 300,
                'alwaysShowNavOnTouchDevices': true
            });

            // Add Intersection Observer for better lazy loading support
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            observer.unobserve(img);
                        }
                    });
                });

                // Convert all images to use data-src and add lazy class
                document.querySelectorAll('img[loading="lazy"]').forEach(img => {
                    img.dataset.src = img.src;
                    img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'; // Tiny placeholder
                    img.classList.add('lazy');
                    imageObserver.observe(img);
                });
            }
        });
    </script>
</body>
</html>
