<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Madil')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/customer/dashboard.css') }}?t={{ time() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customer-layout.css') }}?t={{ time() }}">     
    @yield('styles')
</head>

<body>
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" type="button" arimage.pngia-label="Toggle Sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg glass-navbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/assets/images/logo.png" alt="Madil">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/"><i class="fas fa-home ms-1"></i>الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}" href="/products"><i class="fas fa-store ms-1"></i>المتجر</a>
                    </li>

                    @if(\App\Models\Setting::getBool('show_store_appointments', true))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('appointments*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                            <i class="fas fa-calendar-alt ms-1"></i>مواعيد المتجر
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('client/book') ? 'active' : '' }}" href="{{ route('client.bookings.create') }}">
                            <i class="fas fa-camera ms-1"></i>حجز جلسة تصوير
                        </a>
                    </li>
                </ul>
                <div class="nav-buttons d-flex align-items-center">
                    <a href="/cart" class="btn btn-link position-relative me-3">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                        @if($stats['cart_items_count'] > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $stats['cart_items_count'] }}
                        </span>
                        @endif
                    </a>
                    <a href="/notifications" class="btn btn-link position-relative me-3">
                        <i class="fas fa-bell fa-lg"></i>
                        @if($stats['unread_notifications'] > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $stats['unread_notifications'] }}
                        </span>
                        @endif
                    </a>
                    <a href="{{ route('logout') }}" class="btn btn-outline-primary"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt ms-1"></i>تسجيل الخروج
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="/" class="d-block">
                <img src="/assets/images/logo.png" alt="Madil" class="img-fluid">
            </a>
        </div>
        <div class="sidebar-user-info">
            <!-- <img src="{{ asset('images/default-avatar.png') }}" alt="{{ Auth::user()->name }}" class="user-avatar"> -->
            <h5 class="mb-2">{{ Auth::user()->name }}</h5>
            <span class="badge bg-primary">{{ Auth::user()->role === 'admin' ? 'مدير' : 'عميل' }}</span>
        </div>
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">
                        <i class="fas fa-home"></i>
                        لوحة التحكم
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('products*') ? 'active' : '' }}" href="/products">
                        <i class="fas fa-store"></i>
                        المتجر
                    </a>
                </li>

                @if(\App\Models\Setting::getBool('show_store_appointments', true))
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('appointments*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                        <i class="fas fa-calendar-alt"></i>
                        مواعيد المتجر
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link {{ request()->is('client/book') ? 'active' : '' }}" href="{{ route('client.bookings.create') }}">
                        <i class="fas fa-camera"></i>
                        حجز جلسة تصوير
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('client/bookings/my') ? 'active' : '' }}" href="{{ route('client.bookings.my') }}">
                        <i class="fas fa-camera-retro"></i>
                        حجوزاتي
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('orders*') ? 'active' : '' }}" href="/orders">
                        <i class="fas fa-shopping-bag"></i>
                        طلباتي
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('cart*') ? 'active' : '' }}" href="/cart">
                        <i class="fas fa-shopping-cart"></i>
                        السلة
                        @if($stats['cart_items_count'] > 0)
                        <span class="badge bg-danger ms-auto">{{ $stats['cart_items_count'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('notifications*') ? 'active' : '' }}" href="/notifications">
                        <i class="fas fa-bell"></i>
                        الإشعارات
                        @if($stats['unread_notifications'] > 0)
                        <span class="badge bg-danger ms-auto">{{ $stats['unread_notifications'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('profile*') ? 'active' : '' }}" href="/user/profile">
                        <i class="fas fa-user"></i>
                        الملف الشخصي
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link text-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        تسجيل الخروج
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" style="margin-top: 60px;">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // تهيئة CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Add Sidebar and Navbar Toggle Functionality
        $(document).ready(function() {
            // Sidebar Toggle
            $('.sidebar-toggle').on('click', function() {
                $('.sidebar').toggleClass('show');
            });

            // Navbar Toggle
            $('.navbar-toggler').on('click', function() {
                if ($('.navbar-collapse').hasClass('show')) {
                    $('.navbar-collapse').collapse('hide');
                } else {
                    $('.navbar-collapse').collapse('show');
                }
            });

            // Close sidebar and navbar when clicking outside
            $(document).on('click', function(e) {
                if ($(window).width() < 992) {
                    // Close sidebar
                    if (!$(e.target).closest('.sidebar').length && !$(e.target).closest('.sidebar-toggle').length) {
                        $('.sidebar').removeClass('show');
                    }

                    // Close navbar
                    if (!$(e.target).closest('.navbar-collapse').length && !$(e.target).closest('.navbar-toggler').length) {
                        $('.navbar-collapse').collapse('hide');
                    }
                }
            });
        });
    </script>
    @yield('scripts')
</body>

</html>
