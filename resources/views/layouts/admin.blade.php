<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - مدير مديل</title>

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin-layout.css') }}">
    @yield('styles')
</head>
<body class="h-100">
    <div class="admin-layout">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar shadow-sm" id="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="مدير ماديل" style="width: 60px; height: 60px; object-fit: contain;">
                </a>
            </div>

            <nav class="sidebar-nav">
                <!-- Dashboard Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الرئيسية</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span class="nav-title">لوحة التحكم</span>
                        </a>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="nav-section">
                    <div class="nav-section-title">المنتجات</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i>
                            <span class="nav-title">المنتجات</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-tags"></i>
                            <span class="nav-title">التصنيفات</span>
                        </a>
                    </div>
                </div>

                <!-- Orders Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الطلبات</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="nav-title">الطلبات</span>
                        </a>
                    </div>
                </div>

                <!-- Appointments Section -->
                <div class="nav-section">
                    <div class="nav-section-title">المواعيد</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.appointments.index') }}" class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar"></i>
                            <span class="nav-title">إدارة المواعيد</span>
                        </a>
                    </div>
                </div>

                <!-- Studio Services Section -->
                <div class="nav-section">
                    <div class="nav-section-title">خدمات الاستوديو</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.gallery.index') }}" class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                            <i class="fas fa-photo-video"></i>
                            <span class="nav-title">معرض الصور</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                            <i class="fas fa-camera"></i>
                            <span class="nav-title">الخدمات</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                            <i class="fas fa-gift"></i>
                            <span class="nav-title">الباقات</span>
                        </a>
                    </div>
                </div>

                <!-- Bookings Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الحجوزات</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.bookings.calendar') }}" class="nav-link {{ request()->routeIs('admin.bookings.calendar') ? 'active' : '' }}">
                            <i class="far fa-calendar-check"></i>
                            <span class="nav-title">تقويم الحجوزات</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.bookings.reports') }}" class="nav-link {{ request()->routeIs('admin.bookings.reports') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-title">تقارير الحجوزات</span>
                        </a>
                    </div>
                </div>

                <!-- Reports Section -->
                <div class="nav-section">
                    <div class="nav-section-title">التقارير</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="fas fa-chart-bar"></i>
                            <span class="nav-title">تقارير المتجر</span>
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="{{ route('admin.studio-reports.index') }}" class="nav-link {{ request()->routeIs('admin.studio-reports.*') ? 'active' : '' }}">
                            <i class="fas fa-camera-retro"></i>
                            <span class="nav-title">تقارير الاستوديو</span>
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Mobile Toggle Button -->
        <button class="sidebar-toggle d-lg-none" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Main Content -->
        <main class="main-content-wrapper">
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <div class="d-flex align-items-center">
                        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="مدير ماديل">
                            <span>@yield('page_title', 'لوحة التحكم')</span>
                        </a>
                    </div>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i>
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-cog"></i>
                                        <span>الملف الشخصي</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span>تسجيل الخروج</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Close sidebar on window resize if in mobile view
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 992 && sidebar.classList.contains('active')) {
                toggleSidebar();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
