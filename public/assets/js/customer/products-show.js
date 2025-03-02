let selectedColor = null;
let selectedSize = null;

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

    const useCustomColorCheckbox = document.getElementById('useCustomColor');
    if (useCustomColorCheckbox) {
        useCustomColorCheckbox.checked = false;
        document.getElementById('customColorGroup').classList.add('d-none');
        document.getElementById('customColor').value = '';
    }

    document.querySelectorAll('.color-item').forEach(item => {
        item.classList.remove('active');
    });

    element.classList.add('active');
    selectedColor = element.dataset.color;
}

function selectSize(element) {
    if (!element.classList.contains('available')) return;

    const useCustomSizeCheckbox = document.getElementById('useCustomSize');
    if (useCustomSizeCheckbox) {
        useCustomSizeCheckbox.checked = false;
        document.getElementById('customSizeGroup').classList.add('d-none');
        document.getElementById('customSize').value = '';
    }

    document.querySelectorAll('.size-option').forEach(item => {
        item.classList.remove('active');
    });

    element.classList.add('active');
    selectedSize = element.dataset.size;

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

function showAppointmentModal(cartItemId) {
    if (!getAppointmentsStatus()) {
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

    // إذا تم اختيار مقاس له سعر خاص
    if (selectedSize) {
        const sizeElement = document.querySelector(`.size-option[data-size="${selectedSize}"]`);
        if (sizeElement && sizeElement.dataset.price) {
            currentPrice = parseFloat(sizeElement.dataset.price);
        }
    }

    // تحديث السعر المعروض
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
    const quantity = parseInt(document.getElementById('quantity').value);
    const needsAppointment = document.getElementById('needsAppointment')?.checked || false;

    const appointmentsEnabled = getAppointmentsStatus();
    if (needsAppointment && !appointmentsEnabled) {
        errorMessage.textContent = 'عذراً، ميزة مواعيد المتجر غير متاحة حالياً';
        errorMessage.classList.remove('d-none');
        return;
    }

    const errorMessage = document.getElementById('errorMessage');

    const hasColorSelectionEnabled = document.querySelector('.colors-section') !== null;
    const hasCustomColorEnabled = document.getElementById('customColor') !== null;
    const hasSizeSelectionEnabled = document.querySelector('.available-sizes') !== null;
    const hasCustomSizeEnabled = document.getElementById('customSize') !== null;
    const hasAppointmentEnabled = document.querySelector('.custom-measurements-section') !== null;

    errorMessage.classList.add('d-none');

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

    if (needsAppointment && hasAppointmentEnabled) {
    }
    else if (!needsAppointment) {
        if ((hasColorSelectionEnabled || hasCustomColorEnabled) &&
            !(hasSizeSelectionEnabled || hasCustomSizeEnabled)) {
            if (!colorValue) {
                let errorText = '';
                if (hasColorSelectionEnabled && hasCustomColorEnabled) {
                    errorText = 'يرجى اختيار لون أو كتابة اللون المطلوب';
                } else if (hasColorSelectionEnabled) {
                    errorText = 'يرجى اختيار لون للمنتج';
                } else if (hasCustomColorEnabled) {
                    errorText = 'يرجى كتابة اللون المطلوب';
                }

                if (hasAppointmentEnabled) {
                    errorText += ' أو اختيار خيار "أحتاج إلى أخذ المقاسات"';
                }

                errorMessage.textContent = errorText;
                errorMessage.classList.remove('d-none');
                return;
            }
        }
        else if (!(hasColorSelectionEnabled || hasCustomColorEnabled) &&
                (hasSizeSelectionEnabled || hasCustomSizeEnabled)) {
            if (!sizeValue) {
                let errorText = '';
                if (hasSizeSelectionEnabled && hasCustomSizeEnabled) {
                    errorText = 'يرجى اختيار مقاس أو كتابة المقاس المطلوب';
                } else if (hasSizeSelectionEnabled) {
                    errorText = 'يرجى اختيار مقاس للمنتج';
                } else if (hasCustomSizeEnabled) {
                    errorText = 'يرجى كتابة المقاس المطلوب';
                }

                if (hasAppointmentEnabled) {
                    errorText += ' أو اختيار خيار "أحتاج إلى أخذ المقاسات"';
                }

                errorMessage.textContent = errorText;
                errorMessage.classList.remove('d-none');
                return;
            }
        }
        else if ((hasColorSelectionEnabled || hasCustomColorEnabled) &&
                (hasSizeSelectionEnabled || hasCustomSizeEnabled)) {
            let errorMessages = [];

            if (!colorValue) {
                if (hasColorSelectionEnabled && hasCustomColorEnabled) {
                    errorMessages.push('اختيار لون أو كتابة اللون المطلوب');
                } else if (hasColorSelectionEnabled) {
                    errorMessages.push('اختيار لون للمنتج');
                } else if (hasCustomColorEnabled) {
                    errorMessages.push('كتابة اللون المطلوب');
                }
            }

            if (!sizeValue) {
                if (hasSizeSelectionEnabled && hasCustomSizeEnabled) {
                    errorMessages.push('اختيار مقاس أو كتابة المقاس المطلوب');
                } else if (hasSizeSelectionEnabled) {
                    errorMessages.push('اختيار مقاس للمنتج');
                } else if (hasCustomSizeEnabled) {
                    errorMessages.push('كتابة المقاس المطلوب');
                }
            }

            if (errorMessages.length > 0) {
                let errorText = 'يرجى ' + errorMessages.join(' و');

                if (hasAppointmentEnabled) {
                    errorText += ' أو اختيار خيار "أحتاج إلى أخذ المقاسات"';
                }

                errorMessage.textContent = errorText;
                errorMessage.classList.remove('d-none');
                return;
            }
        }
    }
    else if (needsAppointment && !hasAppointmentEnabled) {
        errorMessage.textContent = 'عذراً، خيار حجز الموعد غير متاح لهذا المنتج';
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
        needs_appointment: needsAppointment
    };

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector('.cart-count').textContent = data.cart_count;
            showNotification(data.message, 'success');

            loadCartItems();

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
            if (document.getElementById('needsAppointment')) {
                document.getElementById('needsAppointment').checked = false;
            }

            if (data.show_modal) {
                document.getElementById('cart_item_id').value = data.cart_item_id;
                const appointmentModal = new bootstrap.Modal(document.getElementById('appointmentModal'));
                appointmentModal.show();
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ أثناء إضافة المنتج إلى السلة', 'error');
    })
    .finally(() => {
        addToCartBtn.innerHTML = originalBtnText;
        addToCartBtn.disabled = false;
    });
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast position-fixed top-0 start-50 translate-middle-x mt-3`;
    notification.style.zIndex = '9999';
    notification.innerHTML = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
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

            updateCartDisplay(data);
            showNotification('تم حذف المنتج من السلة بنجاح', 'success');

            const appointmentModal = document.getElementById('appointmentModal');
            if (appointmentModal && bootstrap.Modal.getInstance(appointmentModal)) {
                appointmentModal.setAttribute('data-allow-close', 'true');
                bootstrap.Modal.getInstance(appointmentModal).hide();
            }
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

        // Handle date change
        dateInput.addEventListener('change', async function() {
            const selectedDate = this.value;
            const timeSelect = document.getElementById('appointment_time');
            const dateSuggestion = document.getElementById('dateSuggestion');
            const suggestionText = document.getElementById('suggestionText');
            const acceptSuggestion = document.getElementById('acceptSuggestion');

            try {
                const response = await fetch(`/appointments/check-availability?date=${selectedDate}`);
                const appointments = await response.json();

                // تحديد الأوقات المحجوزة
                const bookedTimes = appointments.map(app => app.time);

                // التحقق من عدد المواعيد المتاحة
                const availableSlots = getAvailableTimeSlots(selectedDate, bookedTimes);

                if (availableSlots.length === 0) {
                    // البحث عن أقرب يوم متاح
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

                        // عند الضغط على زر القبول
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
                const submitBtn = document.getElementById('submitAppointment');
                const spinner = submitBtn.querySelector('.spinner-border');
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

    // إخفاء/إظهار العناصر المرتبطة بالمواعيد بناءً على حالة الميزة
    const appointmentsEnabled = getAppointmentsStatus();
    const needsAppointmentCheckbox = document.getElementById('needsAppointment');

    // إذا كان عنصر زر حجز المواعيد موجود وكانت الميزة معطلة
    if (needsAppointmentCheckbox) {
        const measurementsSection = document.querySelector('.custom-measurements-section');
        if (!appointmentsEnabled && measurementsSection) {
            measurementsSection.style.display = 'none';
        }
    }

    // إضافة معالج حدث لزر إلغاء الموعد
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
                        // تحديث عدد عناصر السلة
                        document.querySelectorAll('.cart-count').forEach(el => {
                            el.textContent = data.count;
                        });

                        // عرض رسالة نجاح
                        showNotification('تم إلغاء الموعد وإزالة المنتج من السلة', 'success');

                        // إعادة التوجيه حسب السياق
                        const urlParams = new URLSearchParams(window.location.search);
                        if (urlParams.has('pending_appointment')) {
                            window.location.href = '/cart';
                        } else {
                            // إغلاق النافذة المنبثقة وتحديث السلة
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
});

// دالة للبحث عن أقرب يوم متاح
async function findNextAvailableDate(startDate) {
    const maxTries = 30; // نبحث لمدة 30 يوم كحد أقصى
    let currentDate = new Date(startDate);

    for (let i = 1; i <= maxTries; i++) {
        currentDate.setDate(currentDate.getDate() + 1);
        const dateString = currentDate.toISOString().split('T')[0];

        try {
            const response = await fetch(`/appointments/check-availability?date=${dateString}`);
            const appointments = await response.json();

            // التحقق من توفر مواعيد في هذا اليوم
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

// دالة لحساب المواعيد المتاحة في يوم معين
function getAvailableTimeSlots(date, bookedTimes) {
    const workingHours = {
        start: 10, // 10 AM
        end: 18    // 6 PM
    };

    const slots = [];
    const selectedDate = new Date(date);
    const dayOfWeek = selectedDate.getDay();

    // التحقق من أيام العمل (من السبت إلى الخميس)
    if (dayOfWeek !== 5) { // الجمعة = 5
        for (let hour = workingHours.start; hour < workingHours.end; hour++) {
            for (let minute = 0; minute < 60; minute += 30) {
                const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
                if (!bookedTimes.includes(timeString)) {
                    slots.push(timeString);
                }
            }
        }
    }

    return slots;
}

// دالة لتعبئة قائمة الأوقات المتاحة
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
