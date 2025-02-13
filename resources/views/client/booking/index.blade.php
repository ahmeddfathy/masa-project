<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز جلسة تصوير - عدسة سوما</title>
    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/studio-client/booking.css') }}">
</head>
<body>
    @include('parts.navbar')

    <div class="container py-5">
        <!-- Gallery Carousel -->
        <div id="galleryCarousel" class="carousel slide gallery-carousel animate-fadeInUp" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($gallery_images as $key => $image)
                <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="{{ $key }}"
                        class="{{ $key === 0 ? 'active' : '' }}" aria-current="{{ $key === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $key + 1 }}"></button>
                @endforeach
            </div>

            <div class="carousel-inner">
                @foreach($gallery_images as $key => $image)
                <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                    <img src="{{ Storage::url($image->image_url) }}" class="d-block w-100" alt="{{ $image->caption }}">
                    @if($image->caption)
                    <div class="carousel-caption">
                        <h5>{{ $image->caption }}</h5>
                        @if($image->category)
                        <p>{{ $image->category }}</p>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">السابق</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">التالي</span>
            </button>
        </div>

        <!-- Booking Form -->
        <div class="booking-form animate-fadeInUp">
            <h2>حجز جلسة تصوير</h2>

            <form action="{{ route('client.bookings.store') }}" method="POST">
                @csrf
                <input type="hidden" name="intended_route" value="{{ url()->current() }}">

                <!-- Service Selection -->
                <div class="mb-4">
                    <label class="form-label">نوع الجلسة</label>
                    <select name="service_id" class="form-select" required>
                        <option value="">اختر نوع الجلسة</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Package Selection -->
                <div class="mb-4">
                    <label class="form-label">الباقة</label>
                    <div class="row">
                        @foreach($packages as $package)
                            <div class="col-md-6">
                                <div class="package-card {{ old('package_id') == $package->id ? 'selected' : '' }}">
                                    <input type="radio" name="package_id" value="{{ $package->id }}"
                                           class="form-check-input package-select" required
                                           {{ old('package_id') == $package->id ? 'checked' : '' }}>
                                    <h5>{{ $package->name }}</h5>
                                    <p class="text-muted">{{ $package->description }}</p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-clock me-2"></i>المدة: {{ $package->duration }} ساعة</li>
                                        <li><i class="fas fa-images me-2"></i>عدد الصور: {{ $package->num_photos }}</li>
                                        <li><i class="fas fa-tag me-2"></i>السعر: {{ $package->base_price }} درهم</li>
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Addons Selection -->
                <div class="mb-4" id="addons-section">
                    <label class="form-label">الإضافات المتاحة</label>
                    <div class="row">
                        @foreach($addons as $addon)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input type="checkbox" name="addons[{{ $addon->id }}][id]"
                                                   value="{{ $addon->id }}"
                                                   class="form-check-input"
                                                   id="addon-{{ $addon->id }}"
                                                   {{ old('addons.'.$addon->id.'.id') ? 'checked' : '' }}>
                                            <input type="hidden" name="addons[{{ $addon->id }}][quantity]" value="1">
                                            <label class="form-check-label" for="addon-{{ $addon->id }}">
                                                <h6>{{ $addon->name }}</h6>
                                                <p class="text-muted small mb-2">{{ $addon->description }}</p>
                                                <span class="badge bg-primary">{{ $addon->price }} درهم</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Date and Time -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">تاريخ الجلسة</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            <input type="date" name="session_date" class="form-control" required value="{{ old('session_date') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">وقت الجلسة</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-clock"></i></span>
                            <input type="time" name="session_time" class="form-control" required value="{{ old('session_time') }}">
                        </div>
                    </div>
                </div>

                <!-- Baby Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">اسم المولود</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-baby"></i></span>
                            <input type="text" name="baby_name" class="form-control" value="{{ old('baby_name') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">تاريخ الميلاد</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                            <input type="date" name="baby_birth_date" class="form-control" value="{{ old('baby_birth_date') }}">
                        </div>
                    </div>
                </div>

                <!-- Gender -->
                <div class="mb-4">
                    <label class="form-label">الجنس</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                        <select name="gender" class="form-select">
                            <option value="">اختر الجنس</option>
                            <option value="ذكر" {{ old('gender') == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                            <option value="أنثى" {{ old('gender') == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-4">
                    <label class="form-label">ملاحظات إضافية</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-comment"></i></span>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    @auth
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check me-2"></i>تأكيد الحجز
                        </button>
                    @else
                        <button type="submit" class="btn btn-primary btn-lg" formaction="{{ route('client.bookings.save-form') }}">
                            <i class="fas fa-user-plus me-2"></i>تسجيل حساب جديد لإكمال الحجز
                        </button>
                        <p class="mt-3 text-muted">
                            لديك حساب بالفعل؟
                            <button type="submit" class="btn btn-link text-primary p-0" formaction="{{ route('client.bookings.save-form') }}?redirect=login">
                                تسجيل الدخول
                            </button>
                        </p>
                    @endauth
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Package Selection
            document.querySelectorAll('.package-card').forEach(card => {
                card.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                    }
                    document.querySelectorAll('.package-card').forEach(c => {
                        c.classList.remove('selected');
                    });
                    this.classList.add('selected');
                });
            });

            // Form Animation
            document.querySelectorAll('.form-control, .form-select').forEach(element => {
                element.addEventListener('focus', function() {
                    this.closest('.input-group')?.classList.add('focused');
                });
                element.addEventListener('blur', function() {
                    this.closest('.input-group')?.classList.remove('focused');
                });
            });
        });
    </script>
</body>
</html>
