let selectedColor = null;
let selectedSize = null;
let selectedQuantityId = null;
let selectedQuantityValue = null;
let selectedQuantityPrice = null;

function getAppointmentsStatus() {
    return document.getElementById('appointmentsEnabled')?.value === 'true';
}

function updateMainImage(src, thumbnail) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail-wrapper').forEach(thumb => {
        thumb.classList.remove('active');
    });
    if (thumbnail) {
        thumbnail.classList.add('active');
    }
}

function selectColor(element) {
    if (!element.classList.contains('available')) return;

    if (element.classList.contains('active')) {
        element.classList.remove('active');
        selectedColor = null;
        return;
    }

    const useCustomColorCheckbox = document.getElementById('useCustomColor');
    if (useCustomColorCheckbox) {
        useCustomColorCheckbox.checked = false;
        document.getElementById('customColorGroup').classList.add('d-none');
        document.getElementById('customColor').value = '';
        document.getElementById('customColor').disabled = true;
    }

    document.querySelectorAll('.color-item').forEach(item => {
        item.classList.remove('active');
    });

    element.classList.add('active');
    selectedColor = element.dataset.color;
}

function selectSize(element) {
    if (!element.classList.contains('available')) return;

    if (element.classList.contains('active')) {
        element.classList.remove('active');
        selectedSize = null;

        updatePrice();
        return;
    }

    const useCustomSizeCheckbox = document.getElementById('useCustomSize');
    if (useCustomSizeCheckbox) {
        useCustomSizeCheckbox.checked = false;
        document.getElementById('customSizeGroup').classList.add('d-none');
        document.getElementById('customSize').value = '';
        document.getElementById('customSize').disabled = true;
    }

    document.querySelectorAll('.size-option').forEach(item => {
        item.classList.remove('active');
    });

    element.classList.add('active');
    selectedSize = element.dataset.size;

    updatePrice();

    document.getElementById('needsAppointment').checked = false;
}

function updatePageQuantity(change) {
    const quantityInput = document.getElementById('quantity');
    let newQuantity = parseInt(quantityInput.value) + change;
    const maxStock = parseInt(quantityInput.getAttribute('max'));

    if (newQuantity >= 1 && newQuantity <= maxStock) {
        quantityInput.value = newQuantity;
    }
}

function updateWorkingHoursDisplay() {
    if (window.studioWorkingHours) {
        // تحديث النص في شاشة الحجز
        const startTime = window.studioWorkingHours.startFormatted || '10:00';
        const endTime = window.studioWorkingHours.endFormatted || '18:00';

        // تنسيق الوقت بطريقة عربية
        const formatTimeArabic = (timeStr) => {
            const [hours, minutes] = timeStr.split(':');
            const hour = parseInt(hours);

            if (hour < 12) {
                return `${hour}${minutes !== '00' ? `:${minutes}` : ''} صباحاً`;
            } else if (hour === 12) {
                return `${hour}${minutes !== '00' ? `:${minutes}` : ''} ظهراً`;
            } else {
                return `${hour - 12}${minutes !== '00' ? `:${minutes}` : ''} مساءً`;
            }
        };

        // تحديث العناصر في واجهة المستخدم
        const startTimeEl = document.getElementById('studioStartTime');
        const endTimeEl = document.getElementById('studioEndTime');

        if (startTimeEl) startTimeEl.textContent = formatTimeArabic(startTime);
        if (endTimeEl) endTimeEl.textContent = formatTimeArabic(endTime);
    }
}

function showAppointmentModal(cartItemId) {
    // If appointments are not enabled or not needed, just return without doing anything
    if (!getAppointmentsStatus() || !document.getElementById('needsAppointment')?.checked) {
        showNotification('تم إضافة المنتج للسلة بنجاح', 'success');
        return;
    }

    fetch(`/cart/items/${cartItemId}/check-appointment`)
        .then(response => response.json())
        .then(data => {
            if (data.needs_appointment) {
                document.getElementById('cart_item_id').value = cartItemId;
                document.getElementById('appointmentForm').reset();
                document.getElementById('addressField').classList.add('d-none');
                document.getElementById('appointmentErrors').classList.add('d-none');

                // تحميل أوقات العمل مسبقاً
                fetchWorkingHours();

                const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
                modal.show();
            } else {
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('pending_appointment');
                window.history.replaceState({}, '', currentUrl);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('حدث خطأ أثناء التحقق من حالة الموعد', 'error');
        });
}

// دالة جديدة لتحميل أوقات العمل
function fetchWorkingHours() {
    // استخدام تاريخ اليوم فقط لجلب أوقات العمل
    const today = new Date().toISOString().split('T')[0];
    fetch(`/appointments/check-availability?date=${today}`)
        .then(response => response.json())
        .then(data => {
            if (data.workingHours) {
                // تحديد ما إذا كان جدول العمل يمتد عبر منتصف الليل
                const startHour = data.workingHours.start;
                const endHour = data.workingHours.end;
                const isOvernight = endHour < startHour;

                window.studioWorkingHours = {
                    ...data.workingHours,
                    isOvernight: isOvernight
                };

                console.log("Studio working hours:", window.studioWorkingHours);
                updateWorkingHoursDisplay();
            }
        })
        .catch(error => {
            console.error('Error fetching working hours:', error);
        });
}

function closeAppointmentModal() {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('pending_appointment');
    window.history.replaceState({}, '', currentUrl);

    const modal = bootstrap.Modal.getInstance(document.getElementById('appointmentModal'));
    if (modal) {
        modal.hide();
    }
}

function toggleAddress() {
    const location = document.getElementById('location').value;
    const addressField = document.getElementById('addressField');
    const addressInput = document.getElementById('address');

    if (location === 'client_location') {
        addressField.classList.remove('d-none');
        addressInput.setAttribute('required', 'required');
    } else {
        addressField.classList.add('d-none');
        addressInput.removeAttribute('required');
        addressInput.value = '';
    }
}

function updatePrice() {
    const priceElement = document.getElementById('product-price');
    const originalPrice = parseFloat(document.getElementById('original-price').value);
    let currentPrice = originalPrice;
    let sizePrice = 0;
    let quantityPrice = 0;

    // حساب سعر المقاس إذا تم اختياره
    if (selectedSize) {
        const sizeElement = document.querySelector(`.size-option[data-size="${selectedSize}"]`);
        if (sizeElement && sizeElement.dataset.price) {
            sizePrice = parseFloat(sizeElement.dataset.price);
        }
    }

    // حساب سعر الكمية إذا تم اختيارها
    if (selectedQuantityPrice) {
        quantityPrice = parseFloat(selectedQuantityPrice);
    }

    // إذا تم اختيار كلاهما، نجمع السعرين معًا
    if (selectedSize && selectedQuantityId) {
        currentPrice = sizePrice + quantityPrice;
    }
    // إذا تم اختيار واحد فقط، نستخدم سعره
    else if (selectedSize) {
        currentPrice = sizePrice;
    }
    else if (selectedQuantityId) {
        currentPrice = quantityPrice;
    }
    // وإلا نستخدم السعر الأصلي
    else {
        currentPrice = originalPrice;
    }

    priceElement.textContent = currentPrice.toFixed(2) + ' ر.س';
}

document.querySelectorAll('.size-option').forEach(el => {
    el.addEventListener('click', function() {
        selectedSize = this.dataset.size;
        document.querySelectorAll('.size-option').forEach(s => s.classList.remove('active'));
        this.classList.add('active');
        updatePrice();
    });
});

function addToCart() {
    const productId = document.getElementById('product-id').value;
    const quantity = document.getElementById('quantity')?.value || 1;
    const appointmentsEnabled = getAppointmentsStatus();
    const needsAppointmentCheckbox = document.getElementById('needsAppointment');

    // needsAppointment should be false if appointments are not enabled or checkbox doesn't exist
    const needsAppointment = appointmentsEnabled && needsAppointmentCheckbox?.checked;

    const errorMessage = document.getElementById('errorMessage');
    errorMessage.classList.add('d-none');

    const hasColorSelectionEnabled = document.querySelector('.colors-section') !== null;
    const hasCustomColorEnabled = document.getElementById('customColor') !== null;
    const hasSizeSelectionEnabled = document.querySelector('.available-sizes') !== null;
    const hasCustomSizeEnabled = document.getElementById('customSize') !== null;

    const quantityOptions = document.querySelectorAll('.quantity-option');
    const quantityErrorAlert = document.getElementById('quantity-error-alert');

    if (quantityOptions.length > 0 && !selectedQuantityId) {
        showNotification('يرجى اختيار إحدى خيارات الكمية المتاحة', 'error');

        if (quantityErrorAlert) {
            quantityErrorAlert.classList.remove('d-none');
        }

        document.querySelector('.quantity-pricing').scrollIntoView({ behavior: 'smooth', block: 'center' });

        const firstAvailableOption = document.querySelector('.quantity-option.available');
        if (firstAvailableOption) {
            firstAvailableOption.classList.add('highlight');
            setTimeout(() => {
                firstAvailableOption.classList.remove('highlight');
            }, 2000);
        }
        return;
    }

    if (quantityErrorAlert) {
        quantityErrorAlert.classList.add('d-none');
    }

    let colorValue = null;
    if (hasColorSelectionEnabled && selectedColor) {
        colorValue = selectedColor;
    } else if (hasCustomColorEnabled) {
        const customColor = document.getElementById('customColor').value.trim();
        if (customColor) {
            colorValue = customColor;
        }
    }

    let sizeValue = null;
    if (hasSizeSelectionEnabled && selectedSize) {
        sizeValue = selectedSize;
    } else if (hasCustomSizeEnabled) {
        const customSize = document.getElementById('customSize').value.trim();
        if (customSize) {
            sizeValue = customSize;
        }
    }

    // Validate color and size selections
    if ((hasColorSelectionEnabled || hasCustomColorEnabled) && !colorValue) {
        let errorText = '';
        if (hasColorSelectionEnabled && hasCustomColorEnabled) {
            errorText = 'يرجى اختيار لون أو كتابة اللون المطلوب';
        } else if (hasColorSelectionEnabled) {
            errorText = 'يرجى اختيار لون للمنتج';
        } else if (hasCustomColorEnabled) {
            errorText = 'يرجى كتابة اللون المطلوب';
        }
        errorMessage.textContent = errorText;
        errorMessage.classList.remove('d-none');
        return;
    }

    if ((hasSizeSelectionEnabled || hasCustomSizeEnabled) && !sizeValue) {
        let errorText = '';
        if (hasSizeSelectionEnabled && hasCustomSizeEnabled) {
            errorText = 'يرجى اختيار مقاس أو كتابة المقاس المطلوب';
        } else if (hasSizeSelectionEnabled) {
            errorText = 'يرجى اختيار مقاس للمنتج';
        } else if (hasCustomSizeEnabled) {
            errorText = 'يرجى كتابة المقاس المطلوب';
        }
        errorMessage.textContent = errorText;
        errorMessage.classList.remove('d-none');
        return;
    }

    const addToCartBtn = document.querySelector('.btn-primary[onclick="addToCart()"]');
    const originalBtnText = addToCartBtn.innerHTML;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإضافة...';
    addToCartBtn.disabled = true;

    const data = {
        product_id: productId,
        quantity: quantity,
        color: colorValue,
        size: sizeValue,
        quantity_option_id: selectedQuantityId,
        needs_appointment: needsAppointment
    };

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث عدد العناصر في جميع أيقونات السلة
            document.querySelectorAll('.cart-count').forEach(element => {
                element.textContent = data.cart_count;
            });

            // إظهار رسالة النجاح دائماً
            showNotification('تم إضافة المنتج للسلة بنجاح', 'success');

            // إذا كان المنتج يتطلب موعد وخاصية المواعيد مفعلة، نعرض نموذج حجز الموعد
            if (needsAppointment) {
                showAppointmentModal(data.cart_item_id);
            }

            // تحديث محتوى السلة
            loadCartItems();

            // Reset form fields
            if (document.querySelector('.colors-section')) {
                selectedColor = null;
                document.querySelectorAll('.color-item').forEach(item => {
                    item.classList.remove('active');
                });
            }
            if (document.querySelector('.available-sizes')) {
                selectedSize = null;
                document.querySelectorAll('.size-option').forEach(item => {
                    item.classList.remove('active');
                });
            }

            if (document.getElementById('customColor')) {
                document.getElementById('customColor').value = '';
            }
            if (document.getElementById('customSize')) {
                document.getElementById('customSize').value = '';
            }

            document.getElementById('quantity').value = 1;
        } else {
            showNotification(data.message || 'حدث خطأ أثناء إضافة المنتج للسلة', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ أثناء إضافة المنتج إلى السلة', 'error');
    })
    .finally(() => {
        const addToCartBtn = document.querySelector('.btn-primary[onclick="addToCart()"]');
        addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart me-2"></i>أضف إلى السلة';
        addToCartBtn.disabled = false;
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast position-fixed top-0 start-50 translate-middle-x mt-3`;
    notification.style.zIndex = '9999';
    notification.style.opacity = '1';
    notification.innerHTML = message;
    document.body.appendChild(notification);

    // إظهار الإشعار لمدة 15 ثانية
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.5s ease';
        // إزالة العنصر بعد انتهاء التأثير البصري
        setTimeout(() => notification.remove(), 500);
    }, 6000);
}

function updateCartDisplay(data) {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCountElements = document.querySelectorAll('.cart-count');

    cartCountElements.forEach(element => {
        element.textContent = data.count;
    });

    cartTotal.textContent = data.total + ' ر.س';

    cartItems.innerHTML = '';

    if (!data.items || data.items.length === 0) {
        cartItems.innerHTML = `
            <div class="cart-empty text-center p-4">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p class="mb-3">السلة فارغة</p>
                <a href="/products" class="btn btn-primary">تصفح المنتجات</a>
            </div>
        `;
        return;
    }

    data.items.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'cart-item';
        itemElement.dataset.itemId = item.id;

        const additionalInfo = [];
        if (item.color) additionalInfo.push(`اللون: ${item.color}`);
        if (item.size) additionalInfo.push(`المقاس: ${item.size}`);
        if (item.needs_appointment) {
            additionalInfo.push(item.has_appointment ?
                '<span class="text-success"><i class="fas fa-check-circle"></i> تم حجز موعد</span>' :
                '<span class="text-warning"><i class="fas fa-clock"></i> بانتظار حجز موعد</span>');
        }

        itemElement.innerHTML = `
            <div class="cart-item-inner p-3 border-bottom">
                <button class="remove-btn btn btn-link text-danger" onclick="removeFromCart(this, ${item.id})">
                    <i class="fas fa-times"></i>
                </button>
                <div class="d-flex gap-3">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                    <div class="cart-item-details flex-grow-1">
                        <h5 class="cart-item-title mb-2">${item.name}</h5>
                        <div class="cart-item-info mb-2">
                            ${additionalInfo.length > 0 ?
                                `<small class="text-muted">${additionalInfo.join(' | ')}</small>` : ''}
                        </div>
                        <div class="cart-item-price mb-2">${item.price} ر.س</div>
                        <div class="quantity-controls d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${item.id}, -1)">-</button>
                            <input type="number" value="${item.quantity}" min="1"
                                onchange="updateCartQuantity(${item.id}, 0, this.value)"
                                class="form-control form-control-sm quantity-input">
                            <button class="btn btn-sm btn-outline-secondary" onclick="updateCartQuantity(${item.id}, 1)">+</button>
                        </div>
                        <div class="cart-item-subtotal mt-2 text-primary">
                            الإجمالي: ${item.subtotal} ر.س
                        </div>
                    </div>
                </div>
            </div>
        `;
        cartItems.appendChild(itemElement);
    });
}

function updateCartQuantity(itemId, change, newValue = null) {
    const quantityInput = document.querySelector(`[data-item-id="${itemId}"] .quantity-input`);
    const currentValue = parseInt(quantityInput.value);
    let quantity = newValue !== null ? parseInt(newValue) : currentValue + change;

    if (quantity < 1) {
        return;
    }

    const cartItem = quantityInput.closest('.cart-item');
    cartItem.style.opacity = '0.5';

    fetch(`/cart/items/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ quantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            quantityInput.value = quantity;
            const subtotalElement = cartItem.querySelector('.cart-item-subtotal');
            subtotalElement.textContent = `الإجمالي: ${data.item_subtotal} ر.س`;

            const cartTotal = document.getElementById('cartTotal');
            if (cartTotal) {
                cartTotal.textContent = data.cart_total + ' ر.س';
            }

            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                element.textContent = data.cart_count;
            });

            showNotification('تم تحديث الكمية بنجاح', 'success');
        } else {
            quantityInput.value = currentValue;
            showNotification(data.message || 'فشل تحديث الكمية', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        quantityInput.value = currentValue;
        showNotification('حدث خطأ أثناء تحديث الكمية', 'error');
    })
    .finally(() => {
        cartItem.style.opacity = '1';
    });
}

function removeFromCart(button, cartItemId) {
    if (event) {
        event.preventDefault();
    }

    if (!confirm('هل أنت متأكد من حذف هذا المنتج من السلة؟')) {
        return;
    }

    const cartItem = button.closest('.cart-item') || document.querySelector(`[data-item-id="${cartItemId}"]`);
    if (cartItem) {
        cartItem.style.opacity = '0.5';
    }

    fetch(`/cart/remove/${cartItemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (cartItem) {
                cartItem.style.opacity = '0';
                cartItem.style.transform = 'translateX(50px)';
            }

            // تحديث عرض السلة مباشرة
            updateCartDisplay(data);
            showNotification('تم حذف المنتج من السلة بنجاح', 'success');

            const appointmentModal = document.getElementById('appointmentModal');
            if (appointmentModal && bootstrap.Modal.getInstance(appointmentModal)) {
                appointmentModal.setAttribute('data-allow-close', 'true');
                bootstrap.Modal.getInstance(appointmentModal).hide();
            }

            // إعادة تحميل عناصر السلة
            loadCartItems();
        } else {
            if (cartItem) {
                cartItem.style.opacity = '1';
            }
            showNotification(data.message || 'حدث خطأ أثناء حذف المنتج', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (cartItem) {
            cartItem.style.opacity = '1';
        }
        showNotification('حدث خطأ أثناء حذف المنتج', 'error');
    });
}

function openCart() {
    document.getElementById('cartSidebar').classList.add('active');
    document.querySelector('.cart-overlay').classList.add('active');
    document.body.classList.add('cart-open');
}

function closeCart() {
    document.getElementById('cartSidebar').classList.remove('active');
    document.querySelector('.cart-overlay').classList.remove('active');
    document.body.classList.remove('cart-open');
}

function loadCartItems() {
    fetch('/cart/items')
        .then(response => response.json())
        .then(data => {
            updateCartDisplay(data);
        })
        .catch(error => console.error('Error:', error));
}

function showLoginPrompt(loginUrl) {
    const currentUrl = window.location.href;
    const modal = new bootstrap.Modal(document.getElementById('loginPromptModal'));
    document.getElementById('loginButton').href = `${loginUrl}?redirect=${encodeURIComponent(currentUrl)}`;
    modal.show();
}

function updateFeatureVisibility(productFeatures) {
    const colorsSection = document.querySelector('.colors-section');
    const customColorSection = document.querySelector('.custom-color-section');
    const useCustomColorCheckbox = document.getElementById('useCustomColor');
    const customColorGroup = document.getElementById('customColorGroup');

    if (colorsSection) {
        colorsSection.style.display = productFeatures.allow_color_selection ? 'block' : 'none';
    }

    if (customColorSection) {
        customColorSection.style.display = productFeatures.allow_custom_color ? 'block' : 'none';
    }

    if (useCustomColorCheckbox && customColorGroup) {
        useCustomColorCheckbox.closest('.custom-color-input').style.display =
            productFeatures.allow_custom_color ? 'block' : 'none';
    }

    const sizesSection = document.querySelector('.available-sizes');
    const customSizeInput = document.querySelector('.custom-size-input');
    const useCustomSizeCheckbox = document.getElementById('useCustomSize');
    const customSizeGroup = document.getElementById('customSizeGroup');

    if (sizesSection) {
        sizesSection.style.display = productFeatures.allow_size_selection ? 'block' : 'none';
    }

    if (customSizeInput) {
        customSizeInput.style.display = productFeatures.allow_custom_size ? 'block' : 'none';
    }

    const appointmentSection = document.querySelector('.custom-measurements-section');
    if (appointmentSection) {
        appointmentSection.style.display = productFeatures.allow_appointment ? 'block' : 'none';
    }
}

function toggleCart() {
    const cartSidebar = document.getElementById('cartSidebar');
    if (cartSidebar.classList.contains('active')) {
        closeCart();
    } else {
        openCart();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();

    document.getElementById('closeCart').addEventListener('click', closeCart);
    document.getElementById('cartToggle').addEventListener('click', toggleCart);
    document.getElementById('fixedCartBtn').addEventListener('click', toggleCart);

    const form = document.getElementById('appointmentForm');
    const dateInput = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.querySelector('.loading-spinner');
    const errorDiv = document.getElementById('appointmentErrors');
    const dateError = document.getElementById('date-error');
    const timeError = document.getElementById('time-error');

    if (dateInput) {
        const today = new Date();
        const maxDate = new Date();
        maxDate.setDate(today.getDate() + 30);

        dateInput.min = today.toISOString().split('T')[0];
        dateInput.max = maxDate.toISOString().split('T')[0];

        dateInput.addEventListener('change', async function() {
            const selectedDate = this.value;
            const timeSelect = document.getElementById('appointment_time');
            const dateSuggestion = document.getElementById('dateSuggestion');
            const suggestionText = document.getElementById('suggestionText');
            const acceptSuggestion = document.getElementById('acceptSuggestion');

            try {
                // التحقق ما إذا كان اليوم المختار هو يوم الجمعة
                const selectedDay = new Date(selectedDate).getDay();
                if (selectedDay === 5) { // 5 = الجمعة
                    dateSuggestion.classList.remove('d-none');
                    suggestionText.textContent = `يوم الجمعة هو يوم الراحة الأسبوعية. يرجى اختيار يوم آخر.`;

                    // العثور على اليوم التالي المتاح
                    const nextAvailableDate = await findNextAvailableDate(selectedDate);
                    if (nextAvailableDate) {
                        const formattedDate = new Date(nextAvailableDate).toLocaleDateString('ar-SA', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });

                        suggestionText.textContent += ` أقرب يوم متاح هو ${formattedDate}`;

                        acceptSuggestion.onclick = function() {
                            dateInput.value = nextAvailableDate;
                            dateInput.dispatchEvent(new Event('change'));
                            dateSuggestion.classList.add('d-none');
                        };
                    }

                    return;
                }

                const response = await fetch(`/appointments/check-availability?date=${selectedDate}`);
                const data = await response.json();

                // استخدام الهيكل الجديد للبيانات المرسلة من الخادم
                const appointments = data.appointments || [];
                // حفظ أوقات العمل المستلمة من الخادم في متغير عالمي
                if (data.workingHours) {
                    window.studioWorkingHours = data.workingHours;
                    // تحديث عرض أوقات العمل في مودال الحجز
                    updateWorkingHoursDisplay();
                }

                const bookedTimes = appointments.map(app => app.time);

                const availableSlots = getAvailableTimeSlots(selectedDate, bookedTimes);

                if (availableSlots.length === 0) {
                    // هنا يعني أن جميع الفترات محجوزة (لأننا تحققنا قبل ذلك أن اليوم ليس يوم الجمعة)
                    const nextAvailableDate = await findNextAvailableDate(selectedDate);

                    if (nextAvailableDate) {
                        dateSuggestion.classList.remove('d-none');
                        const formattedDate = new Date(nextAvailableDate).toLocaleDateString('ar-SA', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        suggestionText.textContent = `هذا اليوم مكتمل. أقرب يوم متاح هو ${formattedDate}`;

                        acceptSuggestion.onclick = function() {
                            dateInput.value = nextAvailableDate;
                            dateInput.dispatchEvent(new Event('change'));
                            dateSuggestion.classList.add('d-none');
                        };
                    }
                } else {
                    dateSuggestion.classList.add('d-none');
                    populateTimeSelect(timeSelect, availableSlots);
                }
            } catch (error) {
                console.error('Error checking availability:', error);
            }
        });
    }

    const useCustomColorCheckbox = document.getElementById('useCustomColor');
    const customColorGroup = document.getElementById('customColorGroup');

    if (useCustomColorCheckbox) {
        useCustomColorCheckbox.addEventListener('change', function() {
            if (this.checked) {
                customColorGroup.classList.remove('d-none');
                document.querySelectorAll('.color-item').forEach(item => {
                    item.classList.remove('active');
                });
                selectedColor = null;
            } else {
                customColorGroup.classList.add('d-none');
                document.getElementById('customColor').value = '';
            }
        });
    }

    const useCustomSizeCheckbox = document.getElementById('useCustomSize');
    const customSizeGroup = document.getElementById('customSizeGroup');

    if (useCustomSizeCheckbox) {
        useCustomSizeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                customSizeGroup.classList.remove('d-none');
                document.querySelectorAll('.size-item').forEach(item => {
                    item.classList.remove('active');
                });
                selectedSize = null;
            } else {
                customSizeGroup.classList.add('d-none');
                document.getElementById('customSize').value = '';
            }
        });
    }

    const customSizeInput = document.getElementById('customSize');
    if (customSizeInput) {
        customSizeInput.addEventListener('input', function() {
            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage && !errorMessage.classList.contains('d-none')) {
                errorMessage.classList.add('d-none');
            }
        });
    }

    const customColorInput = document.getElementById('customColor');
    if (customColorInput) {
        customColorInput.addEventListener('input', function() {
            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage && !errorMessage.classList.contains('d-none')) {
                errorMessage.classList.add('d-none');
            }
        });
    }

    document.getElementById('needsAppointment')?.addEventListener('change', function(e) {
        if (e.target.checked) {
            document.querySelectorAll('.color-item').forEach(item => {
                item.classList.remove('active');
            });
            selectedColor = null;

            if (document.getElementById('customColor')) {
                document.getElementById('customColor').value = '';
            }

            document.querySelectorAll('.size-option').forEach(item => {
                item.classList.remove('active');
            });
            selectedSize = null;

            if (document.getElementById('customSize')) {
                document.getElementById('customSize').value = '';
            }

            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage && !errorMessage.classList.contains('d-none')) {
                errorMessage.classList.add('d-none');
            }

            showNotification('تم اختيار خيار حجز موعد لأخذ المقاسات', 'success');
        }
    });

    const appointmentModal = document.getElementById('appointmentModal');
    if (appointmentModal) {
        const modal = new bootstrap.Modal(appointmentModal);

        const appointmentForm = document.getElementById('appointmentForm');
        if (appointmentForm) {
            appointmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submitBtn');
                const spinner = submitBtn.querySelector('.loading-spinner');
                const errorDiv = document.getElementById('appointmentErrors');

                submitBtn.disabled = true;
                spinner.classList.remove('d-none');
                errorDiv.classList.add('d-none');

                fetch('/appointments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(new FormData(appointmentForm)))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');

                        const urlParams = new URLSearchParams(window.location.search);
                        const redirectUrl = urlParams.has('pending_appointment') ?
                            '/cart' :
                            (data.redirect_url || '/appointments');

                        setTimeout(() => {
                            window.location.href = redirectUrl;
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'حدث خطأ أثناء حجز الموعد');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorDiv.textContent = error.message;
                    if (error.errors) {
                        const errorList = document.createElement('ul');
                        Object.values(error.errors).forEach(error => {
                            const li = document.createElement('li');
                            li.textContent = error[0];
                            errorList.appendChild(li);
                        });
                        errorDiv.appendChild(errorList);
                    }
                    errorDiv.classList.remove('d-none');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    spinner.classList.add('d-none');
                });
            });
        }
    }

    const productId = document.getElementById('product-id').value;
    fetch(`/products/${productId}/details`)
        .then(response => response.json())
        .then(data => {
            updateFeatureVisibility(data.features);
        })
        .catch(error => console.error('Error:', error));

    const urlParams = new URLSearchParams(window.location.search);
    const pendingAppointment = urlParams.get('pending_appointment');

    if (pendingAppointment) {
        showAppointmentModal(pendingAppointment);
    }

    const appointmentsEnabled = getAppointmentsStatus();
    const needsAppointmentCheckbox = document.getElementById('needsAppointment');

    if (needsAppointmentCheckbox) {
        const measurementsSection = document.querySelector('.custom-measurements-section');
        if (!appointmentsEnabled && measurementsSection) {
            measurementsSection.style.display = 'none';
        }
    }

    const cancelAppointmentBtn = document.getElementById('cancelAppointment');

    if (cancelAppointmentBtn) {
        cancelAppointmentBtn.addEventListener('click', function() {
            if (confirm('هل أنت متأكد من إلغاء حجز الموعد؟ سيتم إزالة المنتج من السلة.')) {
                const cartItemId = document.getElementById('cart_item_id').value;

                fetch(`/cart/remove/${cartItemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.cart-count').forEach(el => {
                            el.textContent = data.count;
                        });

                        showNotification('تم إلغاء الموعد وإزالة المنتج من السلة', 'success');

                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('pending_appointment')) {
                            window.location.href = '/cart';
                        } else {
                            bootstrap.Modal.getInstance(document.getElementById('appointmentModal')).hide();
                            loadCartItems();
                        }
                    } else {
                        throw new Error(data.message || 'حدث خطأ أثناء إلغاء الموعد');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message, 'error');
                });
            }
        });
    }

    const firstQuantityOption = document.querySelector('.quantity-option.available');
    if (firstQuantityOption) {
        selectQuantityOption(firstQuantityOption);
    }
});

async function findNextAvailableDate(startDate) {
    const maxTries = 30;
    let currentDate = new Date(startDate);

    for (let i = 1; i <= maxTries; i++) {
        currentDate.setDate(currentDate.getDate() + 1);
        const dateString = currentDate.toISOString().split('T')[0];

        // تخطي أيام الجمعة
        if (currentDate.getDay() === 5) { // 5 = الجمعة
            continue; // تخطي هذا اليوم والانتقال للتالي
        }

        try {
            const response = await fetch(`/appointments/check-availability?date=${dateString}`);
            const data = await response.json();

            // التعامل مع الهيكل الجديد للبيانات
            const appointments = data.appointments || [];

            // تحديث أوقات العمل إذا كانت موجودة في الاستجابة
            if (data.workingHours) {
                const startHour = data.workingHours.start;
                const endHour = data.workingHours.end;
                const isOvernight = endHour < startHour;

                window.studioWorkingHours = data.workingHours;
                // تحديث عرض أوقات العمل في مودال الحجز
                updateWorkingHoursDisplay();
            }

            const bookedTimes = appointments.map(app => app.time);
            const availableSlots = getAvailableTimeSlots(dateString, bookedTimes);

            if (availableSlots.length > 0) {
                return dateString;
            }
        } catch (error) {
            console.error('Error checking next available date:', error);
        }
    }

    return null;
}

function getAvailableTimeSlots(date, bookedTimes) {
    // استخدام المتغير العام للأوقات إذا كان موجوداً، وإلا استخدام القيم الافتراضية
    const workingHours = window.studioWorkingHours || {
        start: 10,
        end: 18,
        isOvernight: false
    };

    const slots = [];
    const selectedDate = new Date(date);
    const dayOfWeek = selectedDate.getDay();

    // يوم الجمعة (dayOfWeek = 5) هو يوم الراحة الأسبوعية ولا تتوفر فيه مواعيد
    if (dayOfWeek !== 5) {
        // التحقق إذا كان جدول العمل يمتد عبر منتصف الليل
        const isOvernight = workingHours.isOvernight || (workingHours.end < workingHours.start);

        // بداية ساعات الدوام
        let startHour = workingHours.start;
        // نهاية ساعات الدوام (إذا كان عبر منتصف الليل، فسنستخدم 24 ساعة)
        let endHour = isOvernight ? 24 : workingHours.end;

        // إضافة الفترات من وقت البدء إلى منتصف الليل
        for (let hour = startHour; hour < endHour; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                if (!bookedTimes.includes(timeString)) {
                    slots.push(timeString);
                }
            }
        }

        // إذا كان جدول العمل يمتد عبر منتصف الليل، نضيف الفترات من 00:00 إلى وقت النهاية
        if (isOvernight) {
            for (let hour = 0; hour < workingHours.end; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                    if (!bookedTimes.includes(timeString)) {
                        slots.push(timeString);
                    }
                }
            }
        }
    }

    return slots;
}

function populateTimeSelect(selectElement, availableSlots) {
    selectElement.innerHTML = '<option value="">اختر الوقت</option>';
    selectElement.disabled = false;

    availableSlots.forEach(slot => {
        const option = document.createElement('option');
        option.value = slot;
        option.textContent = slot;
        selectElement.appendChild(option);
    });
}

function selectQuantityOption(option) {
    if (option.classList.contains('active')) {
        option.classList.remove('active');
        selectedQuantityId = null;
        selectedQuantityValue = null;
        selectedQuantityPrice = null;

        updatePrice();
        return;
    }

    document.querySelectorAll('.quantity-option').forEach(opt => {
        opt.classList.remove('active');
    });

    option.classList.add('active');

    selectedQuantityId = option.dataset.quantityId;
    selectedQuantityValue = option.dataset.quantityValue;
    selectedQuantityPrice = option.dataset.price;

    updatePrice();

    const quantityErrorAlert = document.getElementById('quantity-error-alert');
    if (quantityErrorAlert) {
        quantityErrorAlert.classList.add('d-none');
    }
}

function toggleCustomSize(checkbox) {
    const customSizeGroup = document.getElementById('customSizeGroup');
    const customSizeInput = document.getElementById('customSize');

    if (checkbox.checked) {
        customSizeGroup.classList.remove('d-none');
        customSizeInput.disabled = false;
        customSizeInput.focus();

        document.querySelectorAll('.size-option').forEach(item => {
            item.classList.remove('active');
        });
        selectedSize = null;

        updatePrice();
    } else {
        customSizeGroup.classList.add('d-none');
        customSizeInput.value = '';
        customSizeInput.disabled = true;
    }
}

function toggleCustomColor(checkbox) {
    const customColorGroup = document.getElementById('customColorGroup');
    const customColorInput = document.getElementById('customColor');

    if (checkbox.checked) {
        customColorGroup.classList.remove('d-none');
        customColorInput.disabled = false;
        customColorInput.focus();

        document.querySelectorAll('.color-item').forEach(item => {
            item.classList.remove('active');
        });
        selectedColor = null;
    } else {
        customColorGroup.classList.add('d-none');
        customColorInput.value = '';
        customColorInput.disabled = true;
    }
}
