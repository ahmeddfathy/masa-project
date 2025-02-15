<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\{
    ProductController,
    OrderController,
    AppointmentController,
    CartController,
    CheckoutController,
    ProfileController,
    NotificationController,
    DashboardController,
    PhoneController,
    AddressController,
    ContactController
};

// Admin Controllers
use App\Http\Controllers\Admin\{
    OrderController as AdminOrderController,
    ProductController as AdminProductController,
    CategoryController as AdminCategoryController,
    AppointmentController as AdminAppointmentController,
    ReportController as AdminReportController,
    DashboardController as AdminDashboardController,
    ServiceController,
    PackageController,
    PackageAddonController,
    BookingController as AdminBookingController,
    GalleryController,
    StudioReportsController
};

// Client Booking Controller
use App\Http\Controllers\Client\BookingController;

// Public Routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Static Pages Routes

// About Page
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/gallery', [App\Http\Controllers\GalleryController::class, 'index'])->name('gallery');

Route::get('/services', function () {
    return view('services');
})->name('services');


// Products Routes (Public)
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/filter', [ProductController::class, 'filter'])->name('filter');
    Route::get('/{product}/details', [ProductController::class, 'getProductDetails'])->name('details');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
});

// Auth Routes
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Common Routes (for all authenticated users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // Customer Routes
    Route::middleware(['role:customer'])->group(function () {


        // Phones
        Route::post('/phones', [PhoneController::class, 'store']);
        Route::get('/phones/{phone}', [PhoneController::class, 'show']);
        Route::put('/phones/{phone}', [PhoneController::class, 'update']);
        Route::delete('/phones/{phone}', [PhoneController::class, 'destroy']);
        Route::post('/phones/{phone}/make-primary', [PhoneController::class, 'makePrimary']);

        // Addresses
        Route::post('/addresses', [AddressController::class, 'store']);
        Route::get('/addresses/{address}', [AddressController::class, 'show']);
        Route::put('/addresses/{address}', [AddressController::class, 'update']);
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
        Route::post('/addresses/{address}/make-primary', [AddressController::class, 'makePrimary']);

        // Cart
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add', [ProductController::class, 'addToCart'])->name('add');
            Route::get('/items', [ProductController::class, 'getCartItems'])->name('items');
            Route::patch('/update/{cartItem}', [CartController::class, 'updateQuantity'])->name('update');
            Route::delete('/remove/{cartItem}', [CartController::class, 'removeItem'])->name('remove');
            Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        });

        // Checkout
        Route::controller(CheckoutController::class)->prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store')->middleware('web');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order:uuid}', [OrderController::class, 'show'])->name('show');
        });

        // Appointments
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/', [AppointmentController::class, 'index'])->name('index');
            Route::get('/create', [AppointmentController::class, 'create'])->name('create');
            Route::post('/', [AppointmentController::class, 'store'])->name('store');
            Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
            Route::delete('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
        });
    });

    // Admin Routes
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Dashboard
            Route::get('/dashboard/', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Products Management
            Route::middleware(['permission:manage products'])->group(function () {
                Route::resource('products', AdminProductController::class);
                Route::resource('categories', AdminCategoryController::class);
            });

            // Orders Management
            Route::middleware(['permission:manage orders'])->group(function () {
                Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
                Route::get('/orders/{order:uuid}', [AdminOrderController::class, 'show'])->name('orders.show');
                Route::put('/orders/{order:uuid}/status', [AdminOrderController::class, 'updateStatus'])
                    ->name('orders.update-status');
                Route::put('/orders/{order:uuid}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])
                    ->name('orders.update-payment-status');
            });
            // Gallery Management
            Route::resource('gallery', GalleryController::class);

            // Appointments Management
            Route::middleware(['permission:manage appointments'])->group(function () {
                Route::resource('appointments', AdminAppointmentController::class)->except(['create', 'store', 'edit', 'update']);
                Route::patch('/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.update-status');
            });

            // Reports Management
            Route::middleware(['permission:manage reports'])->group(function () {
                Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
            });

            // Services Management
            Route::controller(ServiceController::class)->prefix('services')->name('services.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{service}/edit', 'edit')->name('edit');
                Route::put('/{service}', 'update')->name('update');
                Route::delete('/{service}', 'destroy')->name('destroy');
            });

            // Packages Management
            Route::controller(PackageController::class)->prefix('packages')->name('packages.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{package}/edit', 'edit')->name('edit');
                Route::put('/{package}', 'update')->name('update');
                Route::delete('/{package}', 'destroy')->name('destroy');
            });

            // Package Addons Management
            Route::controller(PackageAddonController::class)->prefix('addons')->name('addons.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{addon}/edit', 'edit')->name('edit');
                Route::put('/{addon}', 'update')->name('update');
                Route::delete('/{addon}', 'destroy')->name('destroy');
            });

            // Bookings Management
            Route::controller(AdminBookingController::class)->prefix('bookings')->name('bookings.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{booking}', 'show')->name('show');
                Route::patch('/{booking}/status', 'updateStatus')->name('update-status');

                // إضافة المسارات الجديدة
                Route::get('/calendar/view', 'calendar')->name('calendar');  // تقويم الحجوزات
                Route::get('/reports/view', 'reports')->name('reports');     // تقارير الحجوزات
            });

            // Studio Reports
            Route::get('/studio-reports', [StudioReportsController::class, 'index'])->name('studio-reports.index');
        });
});

Route::post('/appointments', [AppointmentController::class, 'store'])
    ->name('appointments.store');

// Protected Cart Routes
Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart/items', [ProductController::class, 'getCartItems'])->name('cart.items');
    Route::patch('/cart/items/{cartItem}', [ProductController::class, 'updateCartItem'])->name('cart.update-item');
    Route::delete('/cart/remove/{cartItem}', [ProductController::class, 'removeCartItem'])->name('cart.remove-item');
});

// مسارات لوحة تحكم العميل
Route::middleware('client')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    // ... باقي مسارات العميل
});



Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/products/filter', [ProductController::class, 'filter'])->name('products.filter');

    // Booking Routes (Public View)
Route::get('/client/book', [App\Http\Controllers\Client\BookingController::class, 'index'])->name('client.bookings.create');
Route::post('/client/book/save-form', [App\Http\Controllers\Client\BookingController::class, 'saveFormData'])->name('client.bookings.save-form');

// Protected Routes (Require Authentication)
Route::middleware(['auth'])->name('client.')->prefix('client')->group(function () {
    // Protected Booking Routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::post('/store', [App\Http\Controllers\Client\BookingController::class, 'store'])->name('store');
        Route::get('/success/{booking}', [App\Http\Controllers\Client\BookingController::class, 'success'])->name('success');
        Route::get('/my', [App\Http\Controllers\Client\BookingController::class, 'myBookings'])->name('my');
        Route::get('/{booking}', [App\Http\Controllers\Client\BookingController::class, 'show'])->name('show');
    });

    // إضافة مسار جديد للحصول على الإضافات
    Route::get('/packages/{package}/addons', function (App\Models\Package $package) {
        return $package->addons()->where('is_active', true)->get();
    })->name('packages.addons');
});

// مسارات الدفع
Route::prefix('client/bookings/payment')->name('client.bookings.payment.')->group(function () {
    Route::post('callback', [BookingController::class, 'paymentCallback'])->name('callback');
    Route::get('return', [BookingController::class, 'paymentReturn'])->name('return');
});
