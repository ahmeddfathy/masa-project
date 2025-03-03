<?php

use Illuminate\Support\Facades\Route;
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
    ContactController,
    PolicyController,
    HomeController,
    GalleryController
};

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
    GalleryController as AdminGalleryController,
    StudioReportsController,
    SettingController
};

use App\Http\Controllers\Client\BookingController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::name('products.')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('index');
    Route::get('/products/filter', [ProductController::class, 'filter'])->name('filter');
    Route::get('/products/{product}/details', [ProductController::class, 'getProductDetails'])->name('details');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('show');
    Route::post('/products/filter', [ProductController::class, 'filter'])->name('filter');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::name('profile.')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::name('notifications.')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('index');
        Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    Route::middleware(['role:customer'])->group(function () {
        Route::post('/phones', [PhoneController::class, 'store'])->name('phones.store');
        Route::get('/phones/{phone}', [PhoneController::class, 'show'])->name('phones.show');
        Route::put('/phones/{phone}', [PhoneController::class, 'update'])->name('phones.update');
        Route::delete('/phones/{phone}', [PhoneController::class, 'destroy'])->name('phones.destroy');
        Route::post('/phones/{phone}/make-primary', [PhoneController::class, 'makePrimary'])->name('phones.make-primary');

        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::get('/addresses/{address}', [AddressController::class, 'show'])->name('addresses.show');
        Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::post('/addresses/{address}/make-primary', [AddressController::class, 'makePrimary'])->name('addresses.make-primary');

        Route::name('cart.')->group(function () {
            Route::get('/cart', [CartController::class, 'index'])->name('index');
            Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('add');
            Route::get('/cart/items', [ProductController::class, 'getCartItems'])->name('items');
            Route::patch('/cart/update/{cartItem}', [CartController::class, 'updateQuantity'])->name('update');
            Route::delete('/cart/remove/{cartItem}', [CartController::class, 'removeItem'])->name('remove');
            Route::post('/cart/clear', [CartController::class, 'clear'])->name('clear');
        });

        Route::name('checkout.')->group(function () {
            Route::get('/checkout', [CheckoutController::class, 'index'])->name('index');
            Route::post('/checkout', [CheckoutController::class, 'store'])->name('store')->middleware('web');
        });

        Route::name('orders.')->group(function () {
            Route::get('/orders', [OrderController::class, 'index'])->name('index');
            Route::get('/orders/{order:uuid}', [OrderController::class, 'show'])->name('show');
        });

        Route::name('appointments.')->middleware('store_appointments')->group(function () {
            Route::get('/appointments', [AppointmentController::class, 'index'])->name('index');
            Route::post('/appointments', [AppointmentController::class, 'store'])->name('store');
            Route::get('/appointments/check-availability', [AppointmentController::class, 'checkAvailability'])->name('check-availability');
            Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('show');
            Route::delete('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
        });
    });

    Route::name('admin.')->middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::post('/admin/update-fcm-token', [AdminDashboardController::class, 'updateFcmToken'])
            ->name('update-fcm-token')
            ->middleware(['web', 'auth']);

        Route::middleware(['permission:manage products'])->group(function () {
            Route::resource('/admin/products', AdminProductController::class)->names('products');
            Route::resource('/admin/categories', AdminCategoryController::class)->names('categories');
        });

        Route::name('orders.')->middleware(['permission:manage orders'])->group(function () {
            Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/admin/orders/{order:uuid}', [AdminOrderController::class, 'show'])->name('show');
            Route::put('/admin/orders/{order:uuid}/status', [AdminOrderController::class, 'updateStatus'])->name('update-status');
            Route::put('/admin/orders/{order:uuid}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])->name('update-payment-status');
        });

        Route::resource('/admin/gallery', AdminGalleryController::class)->names('gallery');

        Route::middleware('store_appointments')->group(function () {
            Route::name('appointments.')->group(function () {
                Route::get('/admin/appointments', [AdminAppointmentController::class, 'index'])->name('index');
                Route::get('/admin/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('show');
                Route::patch('/admin/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('update-status');
                Route::get('/admin/appointments/calendar/view', [AdminAppointmentController::class, 'calendar'])->name('calendar');
                Route::get('/admin/appointments/reports/view', [AdminAppointmentController::class, 'reports'])->name('reports');
            });

            Route::name('reports.')->group(function () {
                Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('index');
            });
        });

        Route::name('services.')->group(function () {
            Route::get('/admin/services', [ServiceController::class, 'index'])->name('index');
            Route::get('/admin/services/create', [ServiceController::class, 'create'])->name('create');
            Route::post('/admin/services', [ServiceController::class, 'store'])->name('store');
            Route::get('/admin/services/{service}/edit', [ServiceController::class, 'edit'])->name('edit');
            Route::put('/admin/services/{service}', [ServiceController::class, 'update'])->name('update');
            Route::delete('/admin/services/{service}', [ServiceController::class, 'destroy'])->name('destroy');
        });

        Route::name('packages.')->group(function () {
            Route::get('/admin/packages', [PackageController::class, 'index'])->name('index');
            Route::get('/admin/packages/create', [PackageController::class, 'create'])->name('create');
            Route::post('/admin/packages', [PackageController::class, 'store'])->name('store');
            Route::get('/admin/packages/{package}/edit', [PackageController::class, 'edit'])->name('edit');
            Route::put('/admin/packages/{package}', [PackageController::class, 'update'])->name('update');
            Route::delete('/admin/packages/{package}', [PackageController::class, 'destroy'])->name('destroy');
        });

        Route::name('addons.')->group(function () {
            Route::get('/admin/addons', [PackageAddonController::class, 'index'])->name('index');
            Route::get('/admin/addons/create', [PackageAddonController::class, 'create'])->name('create');
            Route::post('/admin/addons', [PackageAddonController::class, 'store'])->name('store');
            Route::get('/admin/addons/{addon}/edit', [PackageAddonController::class, 'edit'])->name('edit');
            Route::put('/admin/addons/{addon}', [PackageAddonController::class, 'update'])->name('update');
            Route::delete('/admin/addons/{addon}', [PackageAddonController::class, 'destroy'])->name('destroy');
        });

        Route::name('bookings.')->group(function () {
            Route::get('/admin/bookings', [AdminBookingController::class, 'index'])->name('index');
            Route::get('/admin/bookings/{booking:uuid}', [AdminBookingController::class, 'show'])->name('show');
            Route::patch('/admin/bookings/{booking:uuid}/status', [AdminBookingController::class, 'updateStatus'])->name('update-status');
            Route::get('/admin/bookings/calendar/view', [AdminBookingController::class, 'calendar'])->name('calendar');
            Route::get('/admin/bookings/reports/view', [AdminBookingController::class, 'reports'])->name('reports');
        });

        Route::get('/admin/studio-reports', [StudioReportsController::class, 'index'])->name('studio-reports.index');

        // مسارات الإعدادات
        Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

        // تحديث تاريخ ووقت الموعد
        Route::patch('/admin/appointments/{appointment}/update-datetime', [App\Http\Controllers\Admin\AppointmentController::class, 'updateDateTime'])
            ->name('appointments.update-datetime');
    });
});

Route::post('/appointments', [AppointmentController::class, 'store'])
    ->name('appointments.store')
    ->middleware('store_appointments');

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart/items', [ProductController::class, 'getCartItems'])->name('cart.items');
    Route::patch('/cart/items/{cartItem}', [ProductController::class, 'updateCartItem'])->name('cart.update-item');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'removeItem'])->name('cart.remove-item');
    Route::get('/cart/items/{cartItem}/check-appointment', [CartController::class, 'checkAppointment'])->name('cart.check-appointment');
});

Route::middleware('client')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

Route::get('/client/book', [BookingController::class, 'index'])->name('client.bookings.create');
Route::post('/client/book/save-form', [BookingController::class, 'saveFormData'])->name('client.bookings.save-form');

Route::name('client.')->middleware(['auth'])->group(function () {
    Route::name('bookings.')->group(function () {
        Route::post('/client/bookings/store', [BookingController::class, 'store'])->name('store');
        Route::get('/client/bookings/success/{booking:uuid}', [BookingController::class, 'success'])->name('success');
        Route::get('/client/bookings/my', [BookingController::class, 'myBookings'])->name('my');
        Route::get('/client/bookings/{booking:uuid}', [BookingController::class, 'show'])->name('show');
        Route::post('/client/bookings/available-slots', [BookingController::class, 'getAvailableTimeSlots'])->name('available-slots');
    });

    Route::get('/client/packages/{package}/addons', function (App\Models\Package $package) {
        return $package->addons()->where('is_active', true)->get();
    })->name('packages.addons');
});

Route::name('client.bookings.payment.')->group(function () {
    Route::post('/client/bookings/payment/callback', [BookingController::class, 'paymentCallback'])->name('callback');
    Route::get('/client/bookings/payment/return', [BookingController::class, 'paymentReturn'])->name('return');
});

Route::post('/client/bookings/available-slots', [BookingController::class, 'getAvailableTimeSlots'])
    ->name('client.bookings.available-slots')
    ->middleware('web');

Route::get('/policy', [PolicyController::class, 'index'])->name('policy');
