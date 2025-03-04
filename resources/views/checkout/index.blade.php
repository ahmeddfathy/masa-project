<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Checkout') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('assets/css/customer/checkout.css') }}">

</head>
<body class="checkout-container">
    <!-- Header -->
    <header class="checkout-header">
        <div class="container">
            <div class="header-content">
                <h2>{{ __('إتمام الطلب') }}</h2>
                <a href="{{ route('cart.index') }}" class="back-to-cart-btn">
                    العودة إلى السلة
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="checkout-content">
        <div class="container">
            <div class="checkout-wrapper">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf

                    @if ($errors->any())
                    <div class="error-container">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="error-message">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="checkout-grid">
                        <!-- Bank Information -->
                        <div class="bank-info-section">
                            <div class="bank-info-header">
                                <i class="fas fa-info-circle"></i>
                                <h3>معلومات الدفع</h3>
                            </div>
                            <div class="bank-info-content">
                                <div class="bank-logo">
                                    <i class="fas fa-university"></i>
                                    <h4>البنك الأهلي السعودي</h4>
                                </div>
                                <div class="account-details">
                                    <div class="detail-item">
                                        <span class="detail-label">رقم الحساب</span>
                                        <span class="detail-value">18900000406701</span>
                                        <button class="copy-btn" data-clipboard="18900000406701">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">الآيبان</span>
                                        <span class="detail-value">SA8710000018900000406701</span>
                                        <button class="copy-btn" data-clipboard="SA8710000018900000406701">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">السويفت</span>
                                        <span class="detail-value">NCBKSAJE</span>
                                        <button class="copy-btn" data-clipboard="NCBKSAJE">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="payment-steps">
                                    <div class="step">
                                        <span class="step-number">1</span>
                                        <span class="step-text">حول المبلغ للحساب</span>
                                    </div>
                                    <div class="step">
                                        <span class="step-number">2</span>
                                        <span class="step-text">أرسل صورة الإيصال</span>
                                    </div>
                                    <div class="step">
                                        <span class="step-number">3</span>
                                        <span class="step-text">انتظر تأكيد الطلب</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="order-summary">
                            <h3>ملخص الطلب</h3>
                            <div class="order-items">
                                @if(Auth::check() && isset($cart))
                                    @foreach($cart->items as $item)
                                    <div class="order-item">
                                        <div class="product-info">
                                            <div class="product-image">
                                                <x-product-image :product="$item->product" size="16" />
                                            </div>
                                            <div class="product-details">
                                                <h4>{{ $item->product->name }}</h4>
                                                <p>الكمية: {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                        <p class="item-price">{{ $item->unit_price }} ريال × {{ $item->quantity }}</p>
                                        <p class="item-subtotal">الإجمالي: {{ $item->subtotal }} ريال</p>
                                    </div>
                                    @endforeach
                                @else
                                    @foreach($products as $product)
                                    <div class="order-item">
                                        <div class="product-info">
                                            <div class="product-image">
                                                @if($product->primary_image)
                                                    <img src="{{ url('storage/' . $product->primary_image->image_path) }}"
                                                        alt="{{ $product->name }}">
                                                @else
                                                    <div class="placeholder-image">
                                                        <svg viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-details">
                                                <h4>{{ $product->name }}</h4>
                                                <p>الكمية: {{ $sessionCart[$product->id] }}</p>
                                            </div>
                                        </div>
                                        <p class="item-price">{{ $product->price }} ريال × {{ $sessionCart[$product->id] }}</p>
                                        <p class="item-subtotal">الإجمالي: {{ $product->price * $sessionCart[$product->id] }} ريال</p>
                                    </div>
                                    @endforeach
                                @endif

                                <div class="d-flex justify-content-between">
                                    <h4>الإجمالي الكلي:</h4>
                                    <span class="total-amount">{{ $cart->total_amount }} ريال</span>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Information -->
                        <div class="shipping-info">
                            <h3>معلومات الشحن</h3>
                            <div class="form-groups">
                                <div class="form-group">
                                    <label for="shipping_address" class="form-label">
                                        عنوان الشحن
                                    </label>
                                    <textarea name="shipping_address" id="shipping_address" rows="4"
                                        class="form-input"
                                        placeholder="أدخل عنوان الشحن الكامل"
                                        required>{{ old('shipping_address', Auth::user()->address ?? '') }}</textarea>
                                    @error('shipping_address')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        رقم الهاتف
                                    </label>
                                    <input type="tel" name="phone" id="phone"
                                        value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                        class="form-input"
                                        placeholder="05xxxxxxxx"
                                        required>
                                    @error('phone')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Payment Method -->
                                <div class="form-group">
                                    <label class="form-label">
                                        طريقة الدفع
                                    </label>
                                    <div class="payment-method">
                                        <div class="payment-info">
                                            <span class="payment-label">الدفع عند الاستلام</span>
                                            <input type="hidden" name="payment_method" value="cash">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="notes" class="form-label">
                                        ملاحظات الطلب (اختياري)
                                    </label>
                                    <textarea name="notes" id="notes" rows="4"
                                        class="form-input"
                                        placeholder="أي ملاحظات إضافية للطلب">{{ old('notes') }}</textarea>
                                    @error('notes')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- إضافة حقل الموافقة على السياسة -->
                                <div class="form-group">
                                    <div class="policy-agreement">
                                        <input type="checkbox"
                                               name="policy_agreement"
                                               id="policy_agreement"
                                               class="form-checkbox"
                                               {{ old('policy_agreement') ? 'checked' : '' }}
                                               required>
                                        <label for="policy_agreement" class="form-label">
                                            أوافق على <a href="{{ route('policy') }}" target="_blank">سياسة الشركة وشروط الخدمة</a>
                                        </label>
                                    </div>
                                    @error('policy_agreement')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Appointment ID field -->
                    <input type="hidden" name="appointment_id" value="{{ session('appointment_id') }}">

                    <div class="checkout-actions">
                        <button type="submit" class="place-order-btn">
                            تأكيد الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            this.classList.add('loading');
        });

        // Copy functionality
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-clipboard');
                navigator.clipboard.writeText(textToCopy).then(() => {
                    // Visual feedback
                    this.classList.add('copied');
                    const originalIcon = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i>';

                    setTimeout(() => {
                        this.classList.remove('copied');
                        this.innerHTML = originalIcon;
                    }, 2000);
                });
            });
        });
    </script>
</body>
</html>
