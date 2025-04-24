/**
 * Checkout JavaScript functionality
 * Handles coupon application and payment method selection
 */

document.addEventListener('DOMContentLoaded', function() {
    // Form loading state
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            this.classList.add('loading');
        });
    }

    // Copy bank information buttons
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

    // Payment method handling
    const cashRadio = document.getElementById('payment_cash');
    const onlineRadio = document.getElementById('payment_online');
    const tabbyRadio = document.getElementById('payment_tabby');
    const submitBtn = document.getElementById('submitBtn');
    const tabbyContainer = document.getElementById('tabby-container');
    const bankInfoSection = document.getElementById('bank-info-section');

    // Coupon elements
    const couponForm = document.getElementById('coupon-form');
    const couponInput = document.getElementById('coupon-input');
    const applyCouponBtn = document.getElementById('apply-coupon-btn');
    const couponError = document.getElementById('coupon-error');
    const couponSuccess = document.getElementById('coupon-success');
    const couponNotApplicable = document.getElementById('coupon-not-applicable');
    const closeNotApplicable = document.getElementById('close-not-applicable');
    const appliedCoupon = document.getElementById('applied-coupon');
    const appliedCouponCode = document.getElementById('applied-coupon-code');
    const couponDiscountValue = document.getElementById('coupon-discount-value');
    const removeCoupon = document.getElementById('remove-coupon');
    const discountRow = document.getElementById('discount-row');
    const discountAmount = document.getElementById('discount-amount');
    const finalPrice = document.getElementById('final-price');
    const subtotalValue = document.getElementById('subtotal-value');

    // Update button text based on payment method
    function updateButtonText() {
        if (onlineRadio && onlineRadio.checked) {
            submitBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i> متابعة للدفع الإلكتروني';
            tabbyContainer.style.display = 'none';
            bankInfoSection.classList.add('hidden');
        } else if (tabbyRadio && tabbyRadio.checked) {
            submitBtn.innerHTML = '<i class="fas fa-shopping-bag me-2"></i> متابعة للدفع مع تابي';
            tabbyContainer.style.display = 'block';
            bankInfoSection.classList.add('hidden');
        } else {
            submitBtn.innerHTML = 'تأكيد الطلب';
            tabbyContainer.style.display = 'none';
            bankInfoSection.classList.remove('hidden');
        }
    }

    // Initialize button text
    if (cashRadio && onlineRadio && tabbyRadio) {
        updateButtonText();

        // Update button text when payment method changes
        cashRadio.addEventListener('change', updateButtonText);
        onlineRadio.addEventListener('change', updateButtonText);
        tabbyRadio.addEventListener('change', updateButtonText);
    }

    // Initialize window.cartData if not already set
    if (!window.cartData) {
        window.cartData = {
            subtotal: 0,
            totalAmount: 0
        };
    }

    // Apply coupon functionality
    if (applyCouponBtn) {
        applyCouponBtn.addEventListener('click', function() {
            let couponCode = couponInput.value.trim();

            if (!couponCode) {
                showCouponError('الرجاء إدخال كود الخصم');
                return;
            }

            // Show loading state
            couponForm.classList.add('loading');
            applyCouponBtn.disabled = true;

            // AJAX call to validate coupon
            fetch('/api/validate-coupon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    coupon_code: couponCode
                })
            })
            .then(response => response.json())
            .then(data => {
                couponForm.classList.remove('loading');
                applyCouponBtn.disabled = false;

                if (data.valid) {
                    applyCoupon(data);
                } else {
                    if (data.message === 'هذا الكوبون لا ينطبق على المنتجات الموجودة في السلة') {
                        showCouponNotApplicable();
                    } else {
                        showCouponError(data.message || 'كود الخصم غير صالح');
                    }
                }
            })
            .catch(error => {
                couponForm.classList.remove('loading');
                applyCouponBtn.disabled = false;
                showCouponError('حدث خطأ أثناء التحقق من الكوبون، الرجاء المحاولة مرة أخرى');
                console.error('Error:', error);
            });
        });
    }

    // Close not applicable message
    if (closeNotApplicable) {
        closeNotApplicable.addEventListener('click', function() {
            couponNotApplicable.style.display = 'none';
        });
    }

    // Remove coupon functionality
    if (removeCoupon) {
        removeCoupon.addEventListener('click', function() {
            // Reset coupon form
            couponInput.value = '';
            appliedCoupon.style.display = 'none';
            discountRow.style.display = 'none';
            couponError.style.display = 'none';
            couponSuccess.style.display = 'none';
            couponNotApplicable.style.display = 'none';

            // Reset price
            const originalPrice = window.cartData.totalAmount;
            finalPrice.textContent = originalPrice.toFixed(2) + ' ريال';
            updateTabbyWidgets(originalPrice);

            // AJAX call to remove coupon from session
            fetch('/checkout/remove-coupon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        });
    }

    // Show coupon error message
    function showCouponError(message) {
        couponError.textContent = message;
        couponError.style.display = 'block';
        couponSuccess.style.display = 'none';
        couponNotApplicable.style.display = 'none';

        setTimeout(() => {
            couponError.style.display = 'none';
        }, 5000);
    }

    // Show coupon not applicable message
    function showCouponNotApplicable() {
        couponNotApplicable.style.display = 'flex';
        couponSuccess.style.display = 'none';
        couponError.style.display = 'none';
        // No timeout - message stays until user takes action
    }

    // Apply coupon to checkout
    function applyCoupon(data) {
        // Show success message
        couponSuccess.textContent = data.message || 'تم تطبيق كود الخصم بنجاح!';
        couponSuccess.style.display = 'block';
        couponError.style.display = 'none';
        couponNotApplicable.style.display = 'none';

        // Update applied coupon section
        appliedCouponCode.textContent = data.coupon.code;

        let discountText = '';
        if (data.coupon.type === 'percentage') {
            discountText = data.coupon.value + '%';
        } else {
            discountText = data.coupon.value + ' ريال';
        }
        couponDiscountValue.textContent = discountText;

        // Show applied coupon section
        appliedCoupon.style.display = 'block';

        // Update price breakdown
        const subtotal = window.cartData.totalAmount || window.cartData.subtotal;
        const discount = parseFloat(data.discount_amount);
        const newTotal = subtotal - discount;

        discountAmount.textContent = '- ' + discount.toFixed(2) + ' ريال';
        discountRow.style.display = 'flex';
        finalPrice.textContent = newTotal.toFixed(2) + ' ريال';

        // Update Tabby widgets with the new price
        updateTabbyWidgets(newTotal);

        // Scroll to show the applied coupon
        appliedCoupon.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Update Tabby payment widgets
    function updateTabbyWidgets(price) {
        if (typeof TabbyPromo === 'undefined') {
            console.error('TabbyPromo is not defined');
            return;
        }

        // Tabby On-Site Messaging Widgets
        new TabbyPromo({
            selector: '#tabby-promotional-widget',
            currency: 'SAR',
            price: price.toString(),
            lang: 'ar',
            source: 'product'
        });

        new TabbyPromo({
            selector: '#tabby-product-widget',
            currency: 'SAR',
            price: price.toString(),
            lang: 'ar',
            source: 'checkout'
        });
    }

    // Initialize Tabby widgets
    if (window.cartData) {
        updateTabbyWidgets(window.cartData.totalAmount);
    }

    // Initialize coupon if exists in session
    if (window.sessionCoupon && window.sessionCoupon.valid) {
        applyCoupon(window.sessionCoupon);
    }
});
