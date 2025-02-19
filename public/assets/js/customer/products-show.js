let selectedColor = null;
let selectedSize = null;

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

    // Uncheck custom color checkbox if exists
    const useCustomColorCheckbox = document.getElementById('useCustomColor');
    if (useCustomColorCheckbox) {
        useCustomColorCheckbox.checked = false;
        document.getElementById('customColorGroup').classList.add('d-none');
        document.getElementById('customColor').value = '';
    }

    // Remove active class from all colors
    document.querySelectorAll('.color-item').forEach(item => {
        item.classList.remove('active');
    });

    // Add active class to selected color
    element.classList.add('active');
    selectedColor = element.dataset.color;
}

function selectSize(element) {
    if (!element.classList.contains('available')) return;

    // Uncheck custom size checkbox if exists
    const useCustomSizeCheckbox = document.getElementById('useCustomSize');
    if (useCustomSizeCheckbox) {
        useCustomSizeCheckbox.checked = false;
        document.getElementById('customSizeGroup').classList.add('d-none');
        document.getElementById('customSize').value = '';
    }

    // Remove active class from all sizes
    document.querySelectorAll('.size-option').forEach(item => {
        item.classList.remove('active');
    });

    // Add active class to selected size
    element.classList.add('active');
    selectedSize = element.dataset.size;

    // If size is selected, uncheck needs appointment
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
    // التحقق من حالة العنصر قبل عرض النافذة
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
                // إذا كان العنصر لا يحتاج لموعد، نزيل المعرف من URL
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
    // إزالة معرف العنصر من الـ URL
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

function addToCart() {
    const needsAppointment = document.getElementById('needsAppointment')?.checked || false;
    const quantity = document.getElementById('quantity').value;
    const errorMessage = document.getElementById('errorMessage');

    // التحقق من وجود أقسام الألوان والمقاسات
    const hasColorSelectionEnabled = document.querySelector('.colors-section') !== null;
    const hasCustomColorEnabled = document.getElementById('customColor') !== null;
    const hasSizeSelectionEnabled = document.querySelector('.available-sizes') !== null;
    const hasCustomSizeEnabled = document.getElementById('customSize') !== null;

    // الحصول على قيمة اللون
    let colorValue = null;
    if (hasColorSelectionEnabled && selectedColor) {
        colorValue = selectedColor;
    } else if (hasCustomColorEnabled) {
        const customColor = document.getElementById('customColor').value.trim();
        if (customColor) {
            colorValue = customColor;
        }
    }

    // الحصول على قيمة المقاس
    let sizeValue = null;
    if (hasSizeSelectionEnabled && selectedSize) {
        sizeValue = selectedSize;
    } else if (hasCustomSizeEnabled) {
        const customSize = document.getElementById('customSize').value.trim();
        if (customSize) {
            sizeValue = customSize;
        }
    }

    // التحقق من الحالات المختلفة
    if (hasColorSelectionEnabled || hasCustomColorEnabled) {
        if (hasSizeSelectionEnabled || hasCustomSizeEnabled) {
            // في حالة وجود خيارات الألوان والمقاسات معاً
            if (!needsAppointment) {
                if (!colorValue) {
                    errorMessage.textContent = 'يرجى اختيار لون للمنتج';
                    errorMessage.classList.remove('d-none');
                    return;
                }
                if (!sizeValue) {
                    errorMessage.textContent = 'يرجى اختيار مقاس للمنتج';
                    errorMessage.classList.remove('d-none');
                    return;
                }
            }
        } else {
            // في حالة وجود خيارات الألوان فقط
            if (!needsAppointment && !colorValue) {
                errorMessage.textContent = 'يرجى اختيار لون للمنتج أو حجز موعد لأخذ المقاسات';
                errorMessage.classList.remove('d-none');
                return;
            }
        }
    } else if (hasSizeSelectionEnabled || hasCustomSizeEnabled) {
        // في حالة وجود خيارات المقاسات فقط
        if (!needsAppointment && !sizeValue) {
            errorMessage.textContent = 'يرجى اختيار مقاس للمنتج أو حجز موعد لأخذ المقاسات';
            errorMessage.classList.remove('d-none');
            return;
        }
    } else if (!needsAppointment) {
        // في حالة عدم وجود خيارات وعدم حجز موعد
        errorMessage.textContent = 'يجب حجز موعد لأخذ المقاسات';
        errorMessage.classList.remove('d-none');
        return;
    }

    // Hide any previous error
    errorMessage.classList.add('d-none');

    // Show loading state
    const addToCartBtn = document.querySelector('.btn-primary[onclick="addToCart()"]');
    const originalBtnText = addToCartBtn.innerHTML;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإضافة...';
    addToCartBtn.disabled = true;

    // Make API call
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: document.getElementById('product-id').value,
            quantity: quantity,
            color: colorValue,
            size: sizeValue,
            needs_appointment: needsAppointment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count and show success message
            document.querySelector('.cart-count').textContent = data.cart_count;
            showNotification(data.message, 'success');

            // Update cart items
            loadCartItems();

            // Reset selections
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

            // Reset custom inputs
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

            // If appointment is needed, show modal
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
        // Reset button state
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

    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function updateCartDisplay(data) {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCountElements = document.querySelectorAll('.cart-count');

    // تحديث عدد العناصر في كل أزرار السلة
    cartCountElements.forEach(element => {
        element.textContent = data.count;
    });

    // تحديث الإجمالي
    cartTotal.textContent = data.total + ' ر.س';

    // تحديث قائمة العناصر
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

        // تحضير معلومات إضافية
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
            // تحديث الكمية والإجمالي الفرعي للعنصر
            quantityInput.value = quantity;
            const subtotalElement = cartItem.querySelector('.cart-item-subtotal');
            subtotalElement.textContent = `الإجمالي: ${data.item_subtotal} ر.س`;

            // تحديث إجمالي السلة
            const cartTotal = document.getElementById('cartTotal');
            if (cartTotal) {
                cartTotal.textContent = data.cart_total + ' ر.س';
            }

            // تحديث عدد العناصر في السلة
            const cartCountElements = document.querySelectorAll('.cart-count');
            cartCountElements.forEach(element => {
                element.textContent = data.cart_count;
            });

            showNotification('تم تحديث الكمية بنجاح', 'success');
        } else {
            // إرجاع القيمة القديمة في حالة الخطأ
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
    // Prevent default button behavior if event is passed
    if (event) {
        event.preventDefault();
    }

    // تأكيد الحذف
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

            // تحديث عرض السلة
            updateCartDisplay(data);
            showNotification('تم حذف المنتج من السلة بنجاح', 'success');

            // If appointment modal is open, close it
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
    // التحكم في قسم الألوان
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

    // التحكم في قسم المقاسات
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

    // التحكم في خيار حجز الموعد
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

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();

    // Setup event listeners for both cart buttons
    document.getElementById('closeCart').addEventListener('click', closeCart);
    document.getElementById('cartToggle').addEventListener('click', toggleCart);
    document.getElementById('fixedCartBtn').addEventListener('click', toggleCart);

    // Initialize appointment date handling
    const appointmentDate = document.getElementById('appointment_date');
    if (appointmentDate) {
        const today = new Date();
        const maxDate = new Date();
        maxDate.setDate(today.getDate() + 30);

        appointmentDate.min = today.toISOString().split('T')[0];
        appointmentDate.max = maxDate.toISOString().split('T')[0];

        // Handle date change
        appointmentDate.addEventListener('change', function() {
            const timeSelect = document.getElementById('appointment_time');
            const dateError = document.getElementById('date-error');

            // Reset time select and validation states
            timeSelect.innerHTML = '<option value="">اختر الوقت المناسب</option>';
            timeSelect.disabled = true;
            dateError.textContent = '';
            this.classList.remove('is-invalid');

            if (!this.value) return;

            // Parse the date correctly for timezone handling
            const [year, month, day] = this.value.split('-').map(Number);
            const selectedDate = new Date(year, month - 1, day); // month is 0-based in JavaScript
            const dayOfWeek = selectedDate.getDay();

            // Arabic day names
            const arabicDays = {
                0: 'الأحد',
                1: 'الإثنين',
                2: 'الثلاثاء',
                3: 'الأربعاء',
                4: 'الخميس',
                5: 'الجمعة',
                6: 'السبت'
            };

            // Check if date is in the past
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (selectedDate < today) {
                this.classList.add('is-invalid');
                dateError.textContent = 'لا يمكن اختيار تاريخ في الماضي';
                timeSelect.disabled = true;
                return;
            }

            // Define time slots based on day
            const slots = dayOfWeek === 5 ? // Friday
                [{ start: '17:00', end: '23:00', label: 'الفترة المسائية' }] :
                [
                    { start: '11:00', end: '14:00', label: 'الفترة الصباحية' },
                    { start: '17:00', end: '23:00', label: 'الفترة المسائية' }
                ];

            // Add day name to time select
            timeSelect.innerHTML = `<option value="">اختر الوقت المناسب ليوم ${arabicDays[dayOfWeek]}</option>`;

            // Generate time slots
            slots.forEach(slot => {
                const group = document.createElement('optgroup');
                group.label = slot.label;

                let currentTime = new Date(`2000-01-01T${slot.start}`);
                const endTime = new Date(`2000-01-01T${slot.end}`);

                // If today, skip past times
                const isToday = selectedDate.toDateString() === new Date().toDateString();
                const now = new Date();

                while (currentTime < endTime) {
                    const option = document.createElement('option');
                    const hours = currentTime.getHours().toString().padStart(2, '0');
                    const minutes = currentTime.getMinutes().toString().padStart(2, '0');
                    const timeValue = `${hours}:${minutes}`;

                    // Skip if time is in the past for today
                    if (isToday) {
                        const slotTime = new Date(selectedDate);
                        slotTime.setHours(parseInt(hours), parseInt(minutes), 0, 0);
                        if (slotTime <= now) {
                            currentTime.setMinutes(currentTime.getMinutes() + 30);
                            continue;
                        }
                    }

                    // Format time in Arabic
                    const timeString = new Date(`2000-01-01T${timeValue}`)
                        .toLocaleTimeString('ar-SA', {
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        });

                    option.value = timeValue;
                    option.textContent = timeString;
                    group.appendChild(option);

                    // Add 30 minutes
                    currentTime.setMinutes(currentTime.getMinutes() + 30);
                }

                // Only add group if it has options
                if (group.children.length > 0) {
                    timeSelect.appendChild(group);
                }
            });

            // Enable time select only if there are available slots
            const hasSlots = timeSelect.querySelectorAll('option').length > 1;
            timeSelect.disabled = !hasSlots;

            if (!hasSlots && isToday) {
                dateError.textContent = 'لا توجد مواعيد متاحة اليوم، يرجى اختيار يوم آخر';
                this.classList.add('is-invalid');
            }
        });

        // Trigger change event if date is already selected
        if (appointmentDate.value) {
            appointmentDate.dispatchEvent(new Event('change'));
        }
    }

    // Custom Color Toggle
    const useCustomColorCheckbox = document.getElementById('useCustomColor');
    const customColorGroup = document.getElementById('customColorGroup');

    if (useCustomColorCheckbox) {
        useCustomColorCheckbox.addEventListener('change', function() {
            if (this.checked) {
                customColorGroup.classList.remove('d-none');
                // Clear existing color selection
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

    // Custom Size Toggle
    const useCustomSizeCheckbox = document.getElementById('useCustomSize');
    const customSizeGroup = document.getElementById('customSizeGroup');

    if (useCustomSizeCheckbox) {
        useCustomSizeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                customSizeGroup.classList.remove('d-none');
                // Clear existing size selection
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

    // Add event listener to needs appointment checkbox
    document.getElementById('needsAppointment').addEventListener('change', function(e) {
        if (e.target.checked) {
            // Clear size selection when appointment is needed
            document.querySelectorAll('.size-item').forEach(item => {
                item.classList.remove('active');
            });
            selectedSize = null;
        }
    });

    // Setup appointment form
    const appointmentModal = document.getElementById('appointmentModal');
    if (appointmentModal) {
        const modal = new bootstrap.Modal(appointmentModal);

        // Handle appointment form submission
        const appointmentForm = document.getElementById('appointmentForm');
        if (appointmentForm) {
            appointmentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = document.getElementById('submitAppointment');
                const spinner = submitBtn.querySelector('.spinner-border');
                const errorDiv = document.getElementById('appointmentErrors');

                // تعطيل زر الإرسال وإظهار مؤشر التحميل
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
                        // عرض رسالة النجاح
                        showNotification(data.message, 'success');

                        // التحقق مما إذا كان المستخدم قادم من صفحة السلة
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
                    // إعادة تعيين حالة الزر
                    submitBtn.disabled = false;
                    spinner.classList.add('d-none');
                });
            });
        }
    }

    // معالجة زر الإلغاء في نافذة حجز المقاسات
    document.getElementById('cancelAppointment')?.addEventListener('click', function() {
        if (confirm('هل أنت متأكد من إلغاء حجز الموعد؟ سيتم إزالة المنتج من السلة.')) {
            const cartItemId = document.getElementById('cart_item_id').value;

            // إزالة المنتج من السلة
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
                    // تحديث عدد العناصر في السلة
                    document.querySelectorAll('.cart-count').forEach(el => {
                        el.textContent = data.cart_count;
                    });

                    // عرض رسالة نجاح
                    showNotification('تم إلغاء الموعد وإزالة المنتج من السلة', 'success');

                    // التحقق مما إذا كان المستخدم قادم من صفحة السلة
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('pending_appointment')) {
                        // إعادة توجيه المستخدم إلى صفحة السلة
                        window.location.href = '/cart';
                    } else {
                        // إغلاق النافذة المنبثقة وتحديث محتوى السلة
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

    // تحميل تفاصيل المنتج وتحديث واجهة المستخدم
    const productId = document.getElementById('product-id').value;
    fetch(`/products/${productId}/details`)
        .then(response => response.json())
        .then(data => {
            updateFeatureVisibility(data.features);
        })
        .catch(error => console.error('Error:', error));

    // إضافة استعادة حالة النافذة المنبثقة عند تحميل الصفحة
    const urlParams = new URLSearchParams(window.location.search);
    const pendingAppointment = urlParams.get('pending_appointment');

    if (pendingAppointment) {
        showAppointmentModal(pendingAppointment);
    }
});
