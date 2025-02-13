<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="عدسة سوما">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
                </li>

                <!-- Services & Products Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('services') || request()->routeIs('products.*') ? 'active' : '' }}"
                       href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        خدماتنا ومنتجاتنا
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                        <li><a class="dropdown-item {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">خدماتنا</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">منتجاتنا</a></li>
                    </ul>
                </li>

                <!-- Gallery & About Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('gallery') || request()->routeIs('about') ? 'active' : '' }}"
                       href="#" id="aboutDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        من نحن
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="aboutDropdown">
                        <li><a class="dropdown-item {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">معرض الصور</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">من نحن</a></li>
                        <li><a class="dropdown-item" href="#contact">اتصل بنا</a></li>
                    </ul>
                </li>

                <!-- Bookings Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('client.bookings.*') ? 'active' : '' }}"
                       href="#" id="bookingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        الحجوزات
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="bookingsDropdown">
                        <li><a class="dropdown-item {{ request()->routeIs('client.bookings.create') ? 'active' : '' }}" href="{{ route('client.bookings.create') }}">احجز الآن</a></li>
                        @auth
                        <li><a class="dropdown-item {{ request()->routeIs('client.bookings.my') ? 'active' : '' }}" href="{{ route('client.bookings.my') }}">حجوزاتي</a></li>
                        @endauth
                    </ul>
                </li>

                <!-- User Account Dropdown -->
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        حسابي
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">تسجيل الخروج</button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="authDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        تسجيل الدخول
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="authDropdown">
                        <li><a class="dropdown-item" href="{{ route('login') }}">تسجيل الدخول</a></li>
                        <li><a class="dropdown-item" href="{{ route('register') }}">التسجيل</a></li>
                    </ul>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
