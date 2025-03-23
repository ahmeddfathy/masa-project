<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Checkout') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ url('assets/css/customer/checkout.css') }}">

    <style>
        /* Payment Method Styles */
        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
            margin-top: 10px;
        }

        .payment-method-option {
            position: relative;
        }

        .payment-method-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .payment-method-label {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method-option input[type="radio"]:checked + .payment-method-label {
            border-color: #21B3B0;
            background-color: rgba(33, 179, 176, 0.05);
        }

        .payment-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            margin-left: 15px;
            background-color: rgba(33, 179, 176, 0.15);
            color: #21B3B0;
            border-radius: 50%;
            font-size: 18px;
        }

        .payment-label {
            font-weight: 600;
            font-size: 16px;
            flex: 1;
        }

        .payment-cards {
            display: flex;
            gap: 5px;
        }

        .payment-cards img {
            height: 24px;
            width: auto;
        }
    </style>

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
                                    <div class="detail-item">
                                        <span class="detail-label">رقم الواتساب للتواصل</span>
                                        <span class="detail-value">+966561667885</span>
                                        <button class="copy-btn" data-clipboard="+966561667885">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="payment-notice">
                                    <p class="important-note">ملاحظة هامة: يجب دفع المبلغ كاملاً لتأكيد الطلب</p>
                                </div>
                                <div class="payment-steps">
                                    <div class="step">
                                        <span class="step-number">1</span>
                                        <span class="step-text">حول المبلغ للحساب</span>
                                    </div>
                                    <div class="step">
                                        <span class="step-number">2</span>
                                        <span class="step-text">أرسل صورة الإيصال عبر الواتساب</span>
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
                                    <div class="payment-methods">
                                        <div class="payment-method-option">
                                            <input type="radio" name="payment_method" id="payment_cash" value="cash"
                                                {{ old('payment_method') == 'cash' ? 'checked' : '' }} checked>
                                            <label for="payment_cash" class="payment-method-label">
                                                <span class="payment-icon"><i class="fas fa-money-bill-wave"></i></span>
                                                <span class="payment-label">الدفع عند الاستلام</span>
                                            </label>
                                        </div>
                                        <div class="payment-method-option">
                                            <input type="radio" name="payment_method" id="payment_online" value="online"
                                                {{ old('payment_method') == 'online' ? 'checked' : '' }}>
                                            <label for="payment_online" class="payment-method-label">
                                                <span class="payment-icon"><i class="fas fa-credit-card"></i></span>
                                                <span class="payment-label">الدفع الإلكتروني</span>
                                                <div class="payment-cards">
                                                    <img src="{{ asset('assets/images/payments/visa.png') }}" alt="Visa">
                                                    <img src="{{ asset('assets/images/payments/mastercard.png') }}" alt="MasterCard">
                                                    <img src="{{ asset('assets/images/payments/mada.png') }}" alt="Mada">
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @error('payment_method')
                                    <p class="error-message">{{ $message }}</p>
                                    @enderror
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

                    <!-- Submit button section -->
                    <div class="checkout-actions">
                        <button type="submit" class="place-order-btn" id="submitBtn">
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

        // Add JavaScript to update the button text based on payment method
        document.addEventListener('DOMContentLoaded', function() {
            const cashRadio = document.getElementById('payment_cash');
            const onlineRadio = document.getElementById('payment_online');
            const submitBtn = document.getElementById('submitBtn');

            function updateButtonText() {
                if (onlineRadio.checked) {
                    submitBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i> متابعة للدفع الإلكتروني';
                } else {
                    submitBtn.innerHTML = 'تأكيد الطلب';
                }
            }

            // Initialize button text
            updateButtonText();

            // Update button text when payment method changes
            cashRadio.addEventListener('change', updateButtonText);
            onlineRadio.addEventListener('change', updateButtonText);
        });
    </script>
</body>
</html>
