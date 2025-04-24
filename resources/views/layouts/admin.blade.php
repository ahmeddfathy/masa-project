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
    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin-layout.css') }}?t={{ time() }}">

    <!-- Toast Notification Styles -->
    <style>
        .toast-container {
            z-index: 9999;
        }
        .toast {
            min-width: 300px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
            opacity: 1;
        }
        .toast.error-toast .toast-header {
            background-color: #dc3545;
            color: white;
        }
        .toast.success-toast .toast-header {
            background-color: #198754;
            color: white;
        }
        .toast .toast-body {
            background-color: #fff;
            border-radius: 0 0 0.375rem 0.375rem;
        }
        .toast .toast-body ul {
            margin-bottom: 0;
            padding-right: 1rem;
            padding-left: 0;
        }
        .toast .toast-body ul li {
            margin-bottom: 0.25rem;
        }
        .toast .btn-close:focus {
            box-shadow: none;
        }
    </style>

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
                    <img src="{{ asset('assets/images/logo.png') }}" alt="لوحة التحكم">
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
                @if(\App\Models\Setting::getBool('show_store_appointments', true))
                <div class="nav-section">
                    <div class="nav-section-title">المواعيد</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.appointments.index') }}" class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar"></i>
                            <span class="nav-title">إدارة المواعيد</span>
                        </a>
                    </div>
                </div>
                @endif

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
                    <li class="nav-item">
                        <a href="{{ route('admin.addons.index') }}" class="nav-link {{ request()->routeIs('admin.addons.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-puzzle-piece"></i>
                            <p>
                                الخدمات الإضافية
                            </p>
                        </a>
                    </li>
                </div>

                <!-- Bookings Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الحجوزات</div>
                    <div class="nav-item">
                        <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.index') || request()->routeIs('admin.bookings.show') ? 'active' : '' }}">
                            <i class="fas fa-list-alt"></i>
                            <span class="nav-title">جميع الحجوزات</span>
                        </a>
                    </div>
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

                <!-- Settings Section -->
                <div class="nav-section">
                    <div class="nav-section-title">الإعدادات</div>
                    <div class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                           href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cog"></i>
                            <span>إعدادات النظام</span>
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
                            <img src="{{ asset('assets/images/logo.png') }}" alt="لوحة التحكم">
                            <span class="ms-2">@yield('page_title', 'لوحة التحكم')</span>
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

    <!-- Toast Notifications Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <!-- Validation Errors Toast -->
        @if ($errors->any())
        <div class="toast error-toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong class="me-auto">خطأ</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Success Toast -->
        @if (session('success'))
        <div class="toast success-toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">تم بنجاح</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Error Toast -->
        @if (session('error'))
        <div class="toast error-toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong class="me-auto">خطأ</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
        @endif
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

        // Initialize toasts
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss toasts after 5 seconds
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(function(toast) {
                setTimeout(function() {
                    const bsToast = new bootstrap.Toast(toast);
                    bsToast._element.addEventListener('hidden.bs.toast', function() {
                        toast.remove();
                    });
                    bsToast.hide();
                }, 5000);
            });

            // Initialize toasts with close button functionality
            const closeButtons = document.querySelectorAll('.toast .btn-close');
            closeButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const toast = this.closest('.toast');
                    const bsToast = new bootstrap.Toast(toast);
                    bsToast.hide();
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
