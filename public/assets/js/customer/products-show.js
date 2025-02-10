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
    document.querySelectorAll('.size-item').forEach(item => {
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
    document.getElementById('cart_item_id').value = cartItemId;
    const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
    modal.show();
}

function toggleAddress() {
    const location = document.getElementById('location').value;
    const addressField = document.getElementById('addressField');

    if (location === 'client_location') {
        addressField.classList.remove('d-none');
        document.getElementById('address').setAttribute('required', 'required');
    } else {
        addressField.classList.add('d-none');
        document.getElementById('address').removeAttribute('required');
    }
}

function addToCart() {
    const needsAppointment = document.getElementById('needsAppointment').checked;
    const quantity = document.getElementById('quantity').value;
    const errorMessage = document.getElementById('errorMessage');

    // Get color value
    let colorValue = selectedColor;
    const customColorInput = document.getElementById('customColor');
    const useCustomColor = document.getElementById('useCustomColor');
    if (customColorInput && (!useCustomColor || useCustomColor.checked)) {
        const customColorValue = customColorInput.value.trim();
        if (customColorValue) {
            colorValue = customColorValue;
        }
    }

    // Get size value
    let sizeValue = selectedSize;
    const customSizeInput = document.getElementById('customSize');
    const useCustomSize = document.getElementById('useCustomSize');
    if (customSizeInput && (!useCustomSize || useCustomSize.checked)) {
        const customSizeValue = customSizeInput.value.trim();
        if (customSizeValue) {
            sizeValue = customSizeValue;
        }
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
            selectedColor = null;
            selectedSize = null;
            document.querySelectorAll('.color-item, .size-item').forEach(item => {
                item.classList.remove('active');
            });

            // Reset custom inputs if they exist
            if (customColorInput) customColorInput.value = '';
            if (customSizeInput) customSizeInput.value = '';

            document.getElementById('quantity').value = 1;
            document.getElementById('needsAppointment').checked = false;

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

    // تحديث عدد العناصر في كل أزرار السلة
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        element.textContent = data.count;
    });

    // تحديث الإجمالي
    cartTotal.textContent = data.total + ' ر.س';

    // تأثير التلاشي عند التحديث
    cartItems.style.opacity = '0';
    setTimeout(() => {
        cartItems.innerHTML = '';

        if (data.items.length === 0) {
            cartItems.innerHTML = `
                <div class="cart-empty text-center p-4">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <p class="mb-3">السلة فارغة</p>
                    <a href="/products" class="btn btn-primary">تصفح المنتجات</a>
                </div>
            `;
        } else {
            data.items.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'cart-item';
                itemElement.dataset.itemId = item.id;
                itemElement.innerHTML = `
                    <div class="cart-item-inner p-3 border-bottom">
                        <button class="remove-btn btn btn-link text-danger" onclick="removeFromCart(this, ${item.id})">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="d-flex gap-3">
                            <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                            <div class="cart-item-details flex-grow-1">
                                <h5 class="cart-item-title mb-2">${item.name}</h5>
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
        cartItems.style.opacity = '1';
    }, 300);
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
    // منع السلوك الافتراضي للزر
    event.preventDefault();

    // تأكيد الحذف
    if (!confirm('هل أنت متأكد من حذف هذا المنتج من السلة؟')) {
        return;
    }

    const cartItem = button.closest('.cart-item');
    cartItem.style.opacity = '0.5';

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
            cartItem.style.opacity = '0';
            cartItem.style.transform = 'translateX(50px)';

            setTimeout(() => {
                // تحديث عرض السلة
                updateCartDisplay(data);
                showNotification('تم حذف المنتج من السلة بنجاح', 'success');
            }, 300);
        } else {
            cartItem.style.opacity = '1';
            showNotification(data.message || 'حدث خطأ أثناء حذف المنتج', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        cartItem.style.opacity = '1';
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

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();

    // Setup event listeners for both cart buttons
    document.getElementById('closeCart').addEventListener('click', closeCart);

    // Cart toggle in navbar
    document.getElementById('cartToggle').addEventListener('click', function() {
        const cartSidebar = document.getElementById('cartSidebar');
        if (cartSidebar.classList.contains('active')) {
            closeCart();
        } else {
            openCart();
        }
    });

    // Fixed cart button
    document.getElementById('fixedCartBtn').addEventListener('click', function() {
        const cartSidebar = document.getElementById('cartSidebar');
        if (cartSidebar.classList.contains('active')) {
            closeCart();
        } else {
            openCart();
        }
    });

    document.querySelector('.cart-overlay')?.addEventListener('click', closeCart);

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

    // Initialize appointment date restrictions
    const appointmentDate = document.getElementById('appointment_date');
    if (appointmentDate) {
        const today = new Date();
        const maxDate = new Date();
        maxDate.setDate(today.getDate() + 30); // Allow booking up to 30 days in advance

        appointmentDate.min = today.toISOString().split('T')[0];
        appointmentDate.max = maxDate.toISOString().split('T')[0];

        // Set default time range
        const appointmentTime = document.getElementById('appointment_time');
        if (appointmentTime) {
            appointmentTime.min = '09:00';
            appointmentTime.max = '21:00';
        }
    }

    // Setup appointment form
    const appointmentModal = document.getElementById('appointmentModal');
    if (appointmentModal) {
        const modal = new bootstrap.Modal(appointmentModal, {
            backdrop: 'static',
            keyboard: false
        });

        // Handle cancel button click
        document.getElementById('cancelAppointment').addEventListener('click', function() {
            if (confirm('هل أنت متأكد من إلغاء حجز الموعد؟ سيتم إزالة المنتج من السلة.')) {
                const cartItemId = document.getElementById('cart_item_id').value;
                // Remove item from cart
                removeFromCart(event.target, cartItemId);
                // Allow modal to be closed
                appointmentModal.setAttribute('data-allow-close', 'true');
                // Hide modal using Bootstrap's hide method
                bootstrap.Modal.getInstance(appointmentModal).hide();
                // Show notification
                showNotification('تم إلغاء الموعد وإزالة المنتج من السلة', 'warning');
            }
        });

        // Prevent modal from being closed by clicking outside
        appointmentModal.addEventListener('hide.bs.modal', function (event) {
            // If modal is being closed programmatically after successful appointment, allow it
            if (event.target.getAttribute('data-allow-close') === 'true') {
                return;
            }
            // Otherwise prevent closing
            event.preventDefault();
        });

        // Handle appointment form submission
        const appointmentForm = document.getElementById('appointmentForm');
        if (appointmentForm) {
            appointmentForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show loading state
                const submitBtn = document.getElementById('submitAppointment');
                const spinner = submitBtn.querySelector('.spinner-border');
                submitBtn.disabled = true;
                spinner.classList.remove('d-none');

                // Clear previous errors
                const errorDiv = document.getElementById('appointmentErrors');
                errorDiv.classList.add('d-none');
                errorDiv.textContent = '';

                const formData = new FormData(this);

                fetch('/appointments', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Allow modal to be closed
                        appointmentModal.setAttribute('data-allow-close', 'true');

                        // Hide modal
                        bootstrap.Modal.getInstance(appointmentModal).hide();

                        // Show success message
                        showNotification(data.message, 'success');

                        // Redirect to appointment details after 2 seconds
                        setTimeout(() => {
                            window.location.href = data.redirect_url || '/appointments';
                        }, 2000);
                    } else {
                        // Show error message
                        errorDiv.textContent = data.message;
                        if (data.errors) {
                            const errorList = document.createElement('ul');
                            Object.values(data.errors).forEach(error => {
                                const li = document.createElement('li');
                                li.textContent = error[0];
                                errorList.appendChild(li);
                            });
                            errorDiv.appendChild(errorList);
                        }
                        errorDiv.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorDiv.textContent = 'حدث خطأ أثناء حجز الموعد. الرجاء المحاولة مرة أخرى.';
                    errorDiv.classList.remove('d-none');
                })
                .finally(() => {
                    // Reset loading state
                    submitBtn.disabled = false;
                    spinner.classList.add('d-none');
                });
            });
        }
    }
});
