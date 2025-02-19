<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>المنتجات - Madil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/customer/products.css') }}">
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
    <nav class="navbar navbar-expand-lg glass-navbar sticky-top">
      <div class="container">
            <a class="navbar-brand" href="/">
               <img src="{{ asset('assets/images/logo.png') }}" alt="Madil" height="70">
            </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
              <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                        <a class="nav-link" href="/">الرئيسية</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link" href="/about">من نحن</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link active" href="/products">المنتجات</a>
                  </li>
                  <li class="nav-item">
                        <a class="nav-link" href="/profile">حسابي</a>
                  </li>
              </ul>
              <div class="nav-buttons">
                    <button class="btn btn-outline-primary position-relative me-2" id="cartToggle">
                      <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                            0
                      </span>
                    </button>
                    @auth
                        <a href="/dashboard" class="btn btn-primary">لوحة التحكم</a>
                    @else
                        <a href="/login" class="btn btn-outline-primary me-2">تسجيل الدخول</a>
                        <a href="/register" class="btn btn-primary">إنشاء حساب</a>
                    @endauth
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
                        <div class="price-range">
                            <input type="range" class="form-range" id="priceRange"
                                min="{{ $priceRange['min'] }}"
                                max="{{ $priceRange['max'] }}"
                                value="{{ $priceRange['max'] }}">
                            <div class="price-labels">
                                <span>{{ number_format($priceRange['min']) }} ر.س</span>
                                <span id="priceValue">{{ number_format($priceRange['max']) }} ر.س</span>
                            </div>
                        </div>
                    </div>
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
                            <a href="{{ route('products.show', $product->slug) }}" class="product-image-wrapper">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                         alt="{{ $product->name }}"
                                         class="product-image">
                                @else
                                    <img src="{{ asset('images/placeholder.jpg') }}"
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
                                <p class="product-price">{{ number_format($product->price, 2) }} ر.س</p>
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
                                <h4 id="modalProductPrice" class="product-price"></h4>
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
              <h5>عن المتجر</h5>
              <p>نقدم خدمات التفصيل والخياطة بأعلى جودة وأفضل الأسعار مع الالتزام بالمواعيد</p>
              <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-whatsapp"></i></a>
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
                <li><a href="#contact">تواصل معنا</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="footer-contact">
              <h5>معلومات التواصل</h5>
              <ul class="list-unstyled">
                <li class="mb-2 d-flex align-items-center">
                  <i class="fas fa-phone-alt ms-2"></i>
                  <span dir="ltr">054 315 4437</span>
                </li>
                <li class="mb-2 d-flex align-items-center">
                  <i class="fas fa-envelope ms-2"></i>
                  <a href="mailto:info@madil-sa.com" class="text-decoration-none">info@madil-sa.com</a>
                </li>
                <li class="d-flex align-items-center">
                  <i class="fas fa-map-marker-alt ms-2"></i>
                  <span>شارع الملك فهد، الرياض، المملكة العربية السعودية</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <div class="container">
          <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} Madil</p>
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
    <script src="{{ asset('assets/js/customer/products.js') }}"></script>
</body>
</html>
