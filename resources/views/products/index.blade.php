<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="تسوق منتجات عدسة سوما - البومات صور فاخرة، مجسمات ثري دي، صور مطبوعة، وهدايا تذكارية. منتجات عالية الجودة لتوثيق أجمل اللحظات العائلية في أبها حي المحالة.">
    <meta name="keywords" content="عدسة سوما، البومات صور، مجسمات ثري دي، صور مطبوعة، هدايا تذكارية، صور عائلية، صور أطفال، استوديو تصوير، أبها، حي المحالة">
    <meta name="author" content="عدسة سوما">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">

    <!-- Open Graph Meta Tags -->
    <meta property="og:site_name" content="عدسة سوما">
    <meta property="og:title" content="متجر عدسة سوما | البومات صور ومجسمات ثري دي في أبها">
    <meta property="og:description" content="تسوق منتجات عدسة سوما - البومات صور فاخرة، مجسمات ثري دي، صور مطبوعة، وهدايا تذكارية. منتجات عالية الجودة لتوثيق أجمل اللحظات العائلية في أبها حي المحالة.">
    <meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="متجر عدسة سوما | البومات صور ومجسمات ثري دي في أبها">
    <meta name="twitter:description" content="تسوق منتجات عدسة سوما - البومات صور فاخرة، مجسمات ثري دي، صور مطبوعة، وهدايا تذكارية. منتجات عالية الجودة لتوثيق أجمل اللحظات العائلية في أبها حي المحالة.">
    <meta name="twitter:image" content="{{ asset('assets/images/logo.png') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>متجر عدسة سوما | البومات صور ومجسمات ثري دي في أبها حي المحالة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/customer/products.css') }}?t={{ time() }}">

</head>
<body class="{{ auth()->check() ? 'user-logged-in' : '' }}">
    <!-- Fixed Buttons Group -->
    <div class="fixed-buttons-group">
        <button class="fixed-cart-btn" id="fixedCartBtn">
            <i class="fas fa-shopping-cart fa-lg"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                0
            </span>
        </button>
        @auth
        <a href="/dashboard" class="fixed-dashboard-btn">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </a>
        @endauth
    </div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg modern-navbar sticky-top">
      <div class="container">
            <a class="navbar-brand" href="/">
               <img src="{{ asset('assets/images/logo.png') }}" alt="Madil" height="70">
            </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <!-- الرئيسية Dropdown -->
                  <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="homeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <span class="nav-icon"><i class="fas fa-home"></i></span>
                          <span class="nav-text">الرئيسية</span>
                          <i class="fas fa-angle-down dropdown-indicator"></i>
                      </a>
                      <ul class="dropdown-menu animated-dropdown" aria-labelledby="homeDropdown">
                          <li><a class="dropdown-item" href="/"><i class="fas fa-home me-2"></i> الرئيسية</a></li>
                          <li><a class="dropdown-item" href="/about"><i class="fas fa-info-circle me-2"></i> من نحن</a></li>
                          <li><a class="dropdown-item" href="/gallery"><i class="fas fa-images me-2"></i> معرض الصور</a></li>
                          <li><a class="dropdown-item" href="/services"><i class="fas fa-concierge-bell me-2"></i> الخدمات</a></li>
                      </ul>
                  </li>
                  <!-- المتجر -->
                  <li class="nav-item">
                      <a class="nav-link active d-flex align-items-center" href="/products">
                          <span class="nav-icon"><i class="fas fa-shopping-bag"></i></span>
                          <span class="nav-text">المتجر</span>
                      </a>
                  </li>
                  <!-- الحجز Dropdown -->
                  <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="bookingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <span class="nav-icon"><i class="fas fa-calendar"></i></span>
                          <span class="nav-text">الحجز</span>
                          <i class="fas fa-angle-down dropdown-indicator"></i>
                      </a>
                      <ul class="dropdown-menu animated-dropdown" aria-labelledby="bookingDropdown">
                          <li><a class="dropdown-item" href="/client/book"><i class="fas fa-calendar-plus me-2"></i> حجز جديد</a></li>
                          @auth
                          <li><a class="dropdown-item" href="/client/bookings/my"><i class="fas fa-calendar-check me-2"></i> حجوزاتي</a></li>
                          @endauth
                      </ul>
                  </li>
              </ul>
              <div class="nav-buttons d-flex align-items-center">
                    <button class="cart-button" id="cartToggle">
                      <span class="cart-icon-wrapper">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count-badge cart-count">0</span>
                      </span>
                    </button>
                    <div class="dropdown profile-dropdown">
                        <button class="profile-button dropdown-toggle" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="profile-icon-wrapper">
                                <i class="fas fa-user"></i>
                            </span>
                        </button>
                        <ul class="dropdown-menu profile-dropdown-menu" aria-labelledby="profileDropdown">
                            @auth
                                <div class="dropdown-user-info">
                                    <div class="dropdown-user-name">{{ Auth::user()->name }}</div>
                                    <div class="dropdown-user-email">{{ Auth::user()->email }}</div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item" href="/dashboard"><i class="fas fa-tachometer-alt me-2"></i> لوحة التحكم</a></li>
                                <div class="dropdown-divider"></div>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item logout-item"><i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج</button>
                                    </form>
                                </li>
                            @else
                                <div class="dropdown-header">
                                    <i class="fas fa-user-circle auth-icon"></i>
                                    <div>تسجيل الدخول للوصول إلى حسابك</div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <li><a class="dropdown-item login-item" href="/login"><i class="fas fa-sign-in-alt me-2"></i> تسجيل الدخول</a></li>
                                <li><a class="dropdown-item register-item" href="/register"><i class="fas fa-user-plus me-2"></i> إنشاء حساب</a></li>
                            @endauth
                        </ul>
                    </div>
              </div>
          </div>
      </div>
    </nav>

    <!-- Toast Notification -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-shopping-cart me-2"></i>
                <strong class="me-auto">تحديث السلة</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                تم إضافة المنتج إلى السلة بنجاح!
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="container-fluid py-4">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-lg-3 filter-sidebar">
                <div class="filter-container">
                    <h3>الفلاتر</h3>
                    <div class="filter-section">
                        <h4>الفئات</h4>
                        @foreach($categories as $category)
                        <div class="category-item mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    value="{{ $category->slug }}"
                                    id="category-{{ $category->id }}"
                                    name="categories[]"
                                    {{ request('category') == $category->slug ? 'checked' : '' }}>
                                <label class="form-check-label d-flex justify-content-between align-items-center"
                                    for="category-{{ $category->id }}">
                                    {{ $category->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $category->products_count }}</span>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="filter-section">
                        <h4>نطاق السعر</h4>
                        <div class="form-group mb-4">
                            <label for="priceRange" class="form-label">السعر</label>
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ number_format($priceRange['min']) }} ر.س</span>
                                <span id="priceValue">{{ number_format($priceRange['max']) }} ر.س</span>
                            </div>
                            <input type="range" class="form-range" id="priceRange"
                                min="{{ $priceRange['min'] }}"
                                max="{{ $priceRange['max'] }}"
                                value="{{ $priceRange['max'] }}">
                        </div>
                    </div>

                    @php
                        $hasDiscountedProducts = false;
                        foreach($products as $product) {
                            if(isset($product->has_coupon) && $product->has_coupon) {
                                $hasDiscountedProducts = true;
                                break;
                            }
                        }
                    @endphp

                    @if($hasDiscountedProducts)
                    <div class="filter-section">
                        <h4>الخصومات</h4>
                        <div class="discount-filter-box">
                            <input class="discount-checkbox" type="checkbox" value="1" id="discountFilter" name="has_discount">
                            <label class="discount-label" for="discountFilter">
                                <div class="discount-icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                                <div class="discount-text">
                                    <span>المنتجات ذات الخصومات</span>
                                    <small>تصفح أفضل العروض والتخفيضات</small>
                                </div>
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <div class="section-header mb-4">
                    <h2>جميع المنتجات</h2>
                    <div class="d-flex gap-3 align-items-center">
                        <select class="form-select glass-select" id="sortSelect">
                            <option value="newest">الأحدث</option>
                            <option value="price-low">السعر: من الأقل للأعلى</option>
                            <option value="price-high">السعر: من الأعلى للأقل</option>
                        </select>
                        <button onclick="resetFilters()" class="btn btn-outline-primary" id="resetFiltersBtn">
                            <i class="fas fa-filter-circle-xmark me-2"></i>
                            إزالة الفلتر
                        </button>
                    </div>
                </div>
                <div class="row g-4" id="productGrid">
                    @foreach($products as $product)
                    <div class="col-md-6 col-lg-4">
                        <div class="product-card">
                            @if(isset($product->has_coupon) && $product->has_coupon)
                                <div class="coupon-badge">
                                    <i class="fas fa-ticket-alt"></i>
                                    <span class="coupon-code">{{ $product->best_coupon['code'] }}</span>
                                    <span class="coupon-value">
                                        @if($product->best_coupon['type'] === 'percentage')
                                            {{ $product->best_coupon['value'] }}%
                                        @else
                                            {{ $product->best_coupon['value'] }} ر.س
                                        @endif
                                    </span>
                                </div>
                            @endif
                            <a href="{{ route('products.show', $product->slug) }}" class="product-image-wrapper">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ url('storage/' . $product->images->first()->image_path) }}"
                                         alt="{{ $product->name }}"
                                         class="product-image">
                                @else
                                    <img src="{{ url('images/placeholder.jpg') }}"
                                         alt="{{ $product->name }}"
                                         class="product-image">
                                @endif
                            </a>
                            <div class="product-details">
                                <div class="product-category">{{ $product->category->name }}</div>
                                <a href="{{ route('products.show', $product->slug) }}" class="product-title text-decoration-none">
                                    <h3>{{ $product->name }}</h3>
                                </a>
                                <div class="product-rating">
                                    <div class="stars" style="--rating: {{ $product['rating'] }}"></div>
                                    <span class="reviews">({{ $product['reviews'] }} تقييم)</span>
                                </div>
                                <p class="product-price">
                                    @if($product->min_price == $product->max_price)
                                        <span class="product-price-tag">
                                            {{ number_format($product->min_price, 2) }} ر.س
                                        </span>
                                    @else
                                        <span class="product-price-tag">

                                            {{ number_format($product->min_price, 2) }} - {{ number_format($product->max_price, 2) }} ر.س
                                        </span>
                                    @endif
                                </p>
                                <div class="product-actions">
                                    <a href="{{ route('products.show', $product->slug) }}" class="order-product-btn">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        طلب المنتج
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center py-5" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-2">جاري تحميل المنتجات...</p>
                </div>

                <!-- No Products Message -->
                <div id="noProductsMessage" class="text-center py-5" style="display: none;">
                    <h3>لا توجد منتجات تطابق الفلتر</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Shopping Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h3>سلة التسوق</h3>
            <button class="close-cart" id="closeCart">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Cart Items Container with Scroll -->
        <div class="cart-items-container">
            <div class="cart-items" id="cartItems">
                <!-- Cart items will be dynamically added here -->
            </div>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                <span>الإجمالي:</span>
                <span id="cartTotal">0 ر.س</span>
            </div>
            <a href="{{ route('checkout.index') }}" class="checkout-btn">
                <i class="fas fa-shopping-cart ml-2"></i>
                إتمام الشراء
            </a>
        </div>
    </div>

    <!-- Cart Overlay -->
    <div class="cart-overlay"></div>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content glass-modal">
                <div class="modal-header border-0">
                    <h5 class="modal-title">تفاصيل المنتج</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div id="productCarousel" class="carousel slide product-carousel" data-bs-ride="carousel">
                                <div class="carousel-inner rounded-3">
                                    <!-- Carousel items will be dynamically added -->
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h3 id="modalProductName" class="product-title mb-3"></h3>
                            <p id="modalProductDescription" class="product-description mb-4"></p>
                            <div class="price-section mb-4">
                                <h4 id="modalProductPrice" class="product-price-tag"><i class="fas fa-battery-three-quarters"></i> <span></span></h4>
                            </div>
                            <div class="quantity-selector mb-4">
                                <label class="form-label">الكمية:</label>
                                <div class="input-group quantity-group">
                                    <button class="btn btn-outline-primary" type="button" id="decreaseQuantity">-</button>
                                    <input type="number" class="form-control text-center" id="productQuantity" value="1" min="1">
                                    <button class="btn btn-outline-primary" type="button" id="increaseQuantity">+</button>
                                </div>
                            </div>

                            <!-- Colors Section -->
                            <div class="colors-section mb-4" id="modalProductColors">
                                <label class="form-label">الألوان المتاحة:</label>
                                <div class="colors-grid">
                                    <!-- Colors will be added dynamically -->
                                </div>
                            </div>

                            <!-- Sizes Section -->
                            <div class="sizes-section mb-4" id="modalProductSizes">
                                <label class="form-label">المقاسات المتاحة:</label>
                                <div class="sizes-grid">
                                    <!-- Sizes will be added dynamically -->
                                </div>
                            </div>

                            <button class="btn add-to-cart-btn w-100" id="modalAddToCart">
                                <i class="fas fa-shopping-cart me-2"></i>
                                أضف للسلة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Prompt Modal -->
    <div class="modal fade" id="loginPromptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تسجيل الدخول مطلوب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>يجب عليك تسجيل الدخول أو إنشاء حساب جديد لتتمكن من طلب المنتج</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">تسجيل الدخول</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">إنشاء حساب جديد</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="glass-footer">
      <div class="container">
        <div class="row">
          <div class="col-lg-4">
            <div class="footer-about">
              <h5>عن الاستوديو</h5>
              <p>نقدم خدمات التصوير الفوتوغرافي والطباعة بأعلى جودة وأفضل الأسعار مع الالتزام بالمواعيد</p>
              <div class="social-links">
                <a href="https://www.instagram.com/lens_soma_studio/?igsh=d2ZvaHZqM2VoMWsw#" class="social-link" aria-label="Instagram">
                  <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="social-link" aria-label="Facebook">
                  <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="social-link" aria-label="Twitter">
                  <i class="fab fa-twitter"></i>
                </a>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="footer-links">
              <h5>روابط سريعة</h5>
              <ul>
                <li><a href="/">الرئيسية</a></li>
                <li><a href="/products">المنتجات</a></li>
                <li><a href="/about">من نحن</a></li>
                <li><a href="/services">خدماتنا</a></li>
                <li><a href="/client/book">حجز موعد</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="footer-contact">
              <h5>معلومات التواصل</h5>
              <ul class="list-unstyled">
                <li class="mb-2 d-flex align-items-center">
                  <i class="fas fa-phone-alt ms-2"></i>
                  <span dir="ltr">+966561667885</span>
                </li>
                <li class="mb-2 d-flex align-items-center">
                  <i class="fas fa-envelope ms-2"></i>
                  <a href="mailto:lens_soma@outlook.sa" class="text-decoration-none">lens_soma@outlook.sa</a>
                </li>
                <li class="d-flex align-items-center">
                  <i class="fas fa-map-marker-alt ms-2"></i>
                  <span>أبها . المحالة</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="container">
          <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} عدسة سوما</p>
        </div>
      </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.appConfig = {
            routes: {
                products: {
                    filter: '{{ route("products.filter") }}',
                    details: '{{ route("products.details", ["product" => "__id__"]) }}'
                }
            }
        };
    </script>
    <script src="{{ asset('assets/js/customer/products.js') }}?t={{ time() }}"></script>
</body>
</html>
