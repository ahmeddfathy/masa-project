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
use App\Http\Controllers\TestNotificationController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/filter', [ProductController::class, 'filter'])->name('filter');
    Route::get('/{product}/details', [ProductController::class, 'getProductDetails'])->name('details');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::post('/filter', [ProductController::class, 'filter'])->name('filter');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    Route::middleware(['role:customer'])->group(function () {
        Route::post('/phones', [PhoneController::class, 'store']);
        Route::get('/phones/{phone}', [PhoneController::class, 'show']);
        Route::put('/phones/{phone}', [PhoneController::class, 'update']);
        Route::delete('/phones/{phone}', [PhoneController::class, 'destroy']);
        Route::post('/phones/{phone}/make-primary', [PhoneController::class, 'makePrimary']);

        Route::post('/addresses', [AddressController::class, 'store']);
        Route::get('/addresses/{address}', [AddressController::class, 'show']);
        Route::put('/addresses/{address}', [AddressController::class, 'update']);
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy']);
        Route::post('/addresses/{address}/make-primary', [AddressController::class, 'makePrimary']);

        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add', [ProductController::class, 'addToCart'])->name('add');
            Route::get('/items', [ProductController::class, 'getCartItems'])->name('items');
            Route::patch('/update/{cartItem}', [CartController::class, 'updateQuantity'])->name('update');
            Route::delete('/remove/{cartItem}', [CartController::class, 'removeItem'])->name('remove');
            Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        });

        Route::controller(CheckoutController::class)->prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store')->middleware('web');
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order:uuid}', [OrderController::class, 'show'])->name('show');
        });

        Route::prefix('appointments')
            ->name('appointments.')
            ->middleware('store_appointments')
            ->group(function () {
                Route::get('/', [AppointmentController::class, 'index'])->name('index');
                Route::get('/create', [AppointmentController::class, 'create'])->name('create');
                Route::post('/', [AppointmentController::class, 'store'])->name('store');
                Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
                Route::delete('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
            });
    });

    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard/', [AdminDashboardController::class, 'index'])->name('dashboard');

            Route::post('/update-fcm-token', [AdminDashboardController::class, 'updateFcmToken'])
                ->name('update-fcm-token')
                ->middleware(['web', 'auth']);

            Route::middleware(['permission:manage products'])->group(function () {
                Route::resource('products', AdminProductController::class);
                Route::resource('categories', AdminCategoryController::class);
            });

            Route::middleware(['permission:manage orders'])->group(function () {
                Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
                Route::get('/orders/{order:uuid}', [AdminOrderController::class, 'show'])->name('orders.show');
                Route::put('/orders/{order:uuid}/status', [AdminOrderController::class, 'updateStatus'])
                    ->name('orders.update-status');
                Route::put('/orders/{order:uuid}/payment-status', [AdminOrderController::class, 'updatePaymentStatus'])
                    ->name('orders.update-payment-status');
            });

            Route::resource('gallery', AdminGalleryController::class);

            Route::middleware('store_appointments')->group(function () {
                Route::controller(AdminAppointmentController::class)
                    ->prefix('appointments')
                    ->name('appointments.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/{appointment}', 'show')->name('show');
                        Route::patch('/{appointment}/status', 'updateStatus')->name('update-status');
                        Route::get('/calendar/view', 'calendar')->name('calendar');
                        Route::get('/reports/view', 'reports')->name('reports');
                    });

                Route::controller(AdminReportController::class)
                    ->prefix('reports')
                    ->name('reports.')
                    ->group(function () {
                        Route::get('/', 'index')->name('index');
                    });
            });

            Route::controller(ServiceController::class)->prefix('services')->name('services.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{service}/edit', 'edit')->name('edit');
                Route::put('/{service}', 'update')->name('update');
                Route::delete('/{service}', 'destroy')->name('destroy');
            });

            Route::controller(PackageController::class)->prefix('packages')->name('packages.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{package}/edit', 'edit')->name('edit');
                Route::put('/{package}', 'update')->name('update');
                Route::delete('/{package}', 'destroy')->name('destroy');
            });

            Route::controller(PackageAddonController::class)->prefix('addons')->name('addons.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{addon}/edit', 'edit')->name('edit');
                Route::put('/{addon}', 'update')->name('update');
                Route::delete('/{addon}', 'destroy')->name('destroy');
            });

            Route::controller(AdminBookingController::class)->prefix('bookings')->name('bookings.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{booking:uuid}', 'show')->name('show');
                Route::patch('/{booking:uuid}/status', 'updateStatus')->name('update-status');
                Route::get('/calendar/view', 'calendar')->name('calendar');
                Route::get('/reports/view', 'reports')->name('reports');
            });

            Route::get('/studio-reports', [StudioReportsController::class, 'index'])->name('studio-reports.index');
        });
});

Route::post('/appointments', [AppointmentController::class, 'store'])
    ->name('appointments.store')
    ->middleware('store_appointments');

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/cart/add', [ProductController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart/items', [ProductController::class, 'getCartItems'])->name('cart.items');
    Route::patch('/cart/items/{cartItem}', [ProductController::class, 'updateCartItem'])->name('cart.update-item');
    Route::delete('/cart/remove/{cartItem}', [ProductController::class, 'removeCartItem'])->name('cart.remove-item');
    Route::get('/cart/items/{cartItem}/check-appointment', [CartController::class, 'checkAppointment']);
});

Route::middleware('client')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

Route::get('/client/book', [BookingController::class, 'index'])->name('client.bookings.create');
Route::post('/client/book/save-form', [BookingController::class, 'saveFormData'])->name('client.bookings.save-form');

Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::post('/store', [BookingController::class, 'store'])->name('store');
        Route::get('/success/{booking:uuid}', [BookingController::class, 'success'])->name('success');
        Route::get('/my', [BookingController::class, 'myBookings'])->name('my');
        Route::get('/{booking:uuid}', [BookingController::class, 'show'])->name('show');
        Route::post('/available-slots', [BookingController::class, 'getAvailableTimeSlots'])->name('available-slots');
    });

    Route::get('/packages/{package}/addons', function (App\Models\Package $package) {
        return $package->addons()->where('is_active', true)->get();
    })->name('packages.addons');
});

Route::prefix('client/bookings/payment')->name('client.bookings.payment.')->group(function () {
    Route::post('callback', [BookingController::class, 'paymentCallback'])->name('callback');
    Route::get('return', [BookingController::class, 'paymentReturn'])->name('return');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.settings.index');
    Route::post('/admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');
});

Route::post('/client/bookings/available-slots', [BookingController::class, 'getAvailableTimeSlots'])
    ->name('client.bookings.available-slots')
    ->middleware('web');

Route::get('/policy', [PolicyController::class, 'index'])->name('policy');
