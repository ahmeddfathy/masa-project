<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="/assets/images/logo.png" alt="عدسة سوما" loading="lazy">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home"></i> الرئيسية
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('services') || request()->routeIs('about') ? 'active' : '' }}"
                       href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-info-circle"></i> من نحن
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                        <li><a class="dropdown-item {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">
                            <i class="fas fa-clipboard-list"></i> الخدمات
                        </a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">
                            <i class="fas fa-building"></i> من نحن
                        </a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">
                        <i class="fas fa-images"></i> معرض الصور
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('client.bookings.*') ? 'active' : '' }}"
                       href="#" id="studioDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-camera"></i> الاستوديو
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="studioDropdown">
                        <li><a class="dropdown-item {{ request()->routeIs('client.bookings.create') ? 'active' : '' }}" href="{{ route('client.bookings.create') }}">احجز جلسة تصوير</a></li>
                        @auth
                        <li><a class="dropdown-item {{ request()->routeIs('client.bookings.my') ? 'active' : '' }}" href="{{ route('client.bookings.my') }}">حجوزاتي</a></li>
                        @endauth
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('products.*') ? 'active' : '' }}"
                       href="#" id="shopDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-shopping-cart"></i> المتجر
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="shopDropdown">
                        <li><a class="dropdown-item {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">منتجاتنا</a></li>
                        @auth
                        <li><a class="dropdown-item {{ request()->routeIs('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}">عربة التسوق</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('orders.index') ? 'active' : '' }}" href="{{ route('orders.index') }}">طلباتي</a></li>
                        @endauth
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i>
                        @auth
                            {{ Auth::user()->name }}
                        @else
                            حسابي
                        @endauth
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="userDropdown">
                        @auth
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> لوحة التحكم
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user-edit"></i> الملف الشخصي
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('notifications.index') }}">
                            <i class="fas fa-bell"></i> الإشعارات
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                                </button>
                            </form>
                        </li>
                        @else
                        <li><a class="dropdown-item" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> التسجيل
                        </a></li>
                        @endauth
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
