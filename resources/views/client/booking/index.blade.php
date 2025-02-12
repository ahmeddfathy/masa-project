<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز جلسة تصوير - Lense Soma Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/booking.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="/images/logo.png" alt="Lense Soma Studio">
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row">
            <!-- Gallery Preview -->
            <div class="col-md-12 gallery-preview">
                <div class="row">
                    <div class="col-md-4">
                        <img src="/images/newborn1.jpg" alt="Newborn Photography">
                    </div>
                    <div class="col-md-4">
                        <img src="/images/newborn2.jpg" alt="Newborn Photography">
                    </div>
                    <div class="col-md-4">
                        <img src="/images/newborn3.jpg" alt="Newborn Photography">
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="col-md-8 mx-auto">
                <div class="booking-form">
                    <h2 class="text-center mb-4" style="color: var(--primary-color)">حجز جلسة تصوير</h2>

                    <form action="{{ route('client.bookings.store') }}" method="POST">
                        @csrf

                        <!-- Service Selection -->
                        <div class="mb-4">
                            <label class="form-label">نوع الجلسة</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">اختر نوع الجلسة</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Package Selection -->
                        <div class="mb-4">
                            <label class="form-label">الباقة</label>
                            <div class="row">
                                @foreach($packages as $package)
                                    <div class="col-md-6">
                                        <div class="package-card">
                                            <input type="radio" name="package_id" value="{{ $package->id }}"
                                                   class="form-check-input package-select" required>
                                            <h5>{{ $package->name }}</h5>
                                            <p class="text-muted">{{ $package->description }}</p>
                                            <ul class="list-unstyled">
                                                <li>المدة: {{ $package->duration }} ساعة</li>
                                                <li>عدد الصور: {{ $package->num_photos }}</li>
                                                <li>السعر: {{ $package->base_price }} درهم</li>
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Addons Selection -->
                        <div class="mb-4" id="addons-section" style="display: none;">
                            <label class="form-label">الإضافات المتاحة</label>
                            <div class="row" id="addons-container">
                                <!-- الإضافات ستظهر هنا -->
                            </div>
                        </div>

                        <!-- Date and Time -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">تاريخ الجلسة</label>
                                <input type="date" name="session_date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">وقت الجلسة</label>
                                <input type="time" name="session_time" class="form-control" required>
                            </div>
                        </div>

                        <!-- Baby Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">اسم المولود</label>
                                <input type="text" name="baby_name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاريخ الميلاد</label>
                                <input type="date" name="baby_birth_date" class="form-control">
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="mb-4">
                            <label class="form-label">الجنس</label>
                            <select name="gender" class="form-select">
                                <option value="">اختر الجنس</option>
                                <option value="ذكر">ذكر</option>
                                <option value="أنثى">أنثى</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label">ملاحظات إضافية</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                تأكيد الحجز
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const packageInputs = document.querySelectorAll('.package-select');
            const addonsSection = document.getElementById('addons-section');
            const addonsContainer = document.getElementById('addons-container');

            packageInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.checked) {
                        fetchAddons(this.value);
                    }
                });
            });

            function fetchAddons(packageId) {
                fetch(`/client/packages/${packageId}/addons`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(addons => {
                        if (addons.length > 0) {
                            addonsContainer.innerHTML = addons.map(addon => `
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input type="checkbox" name="addons[${addon.id}][id]" value="${addon.id}"
                                                       class="form-check-input" id="addon-${addon.id}">
                                                <input type="hidden" name="addons[${addon.id}][quantity]" value="1">
                                                <label class="form-check-label" for="addon-${addon.id}">
                                                    <h6 class="mb-1">${addon.name}</h6>
                                                    <p class="text-muted small mb-1">${addon.description || ''}</p>
                                                    <span class="badge bg-primary">${addon.price} درهم</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                            addonsSection.style.display = 'block';
                        } else {
                            addonsContainer.innerHTML = '<div class="col-12"><p class="text-muted">لا توجد إضافات متاحة لهذه الباقة</p></div>';
                            addonsSection.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        addonsContainer.innerHTML = '<div class="col-12"><p class="text-danger">حدث خطأ في تحميل الإضافات</p></div>';
                    });
            }

            document.querySelectorAll('.package-card').forEach(card => {
                card.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                    document.querySelectorAll('.package-card').forEach(c => {
                        c.classList.remove('selected');
                    });
                    this.classList.add('selected');
                });
            });
        });
    </script>
</body>
</html>
