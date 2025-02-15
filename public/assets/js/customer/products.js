// Filter and Sort Functions
let activeFilters = {
    categories: [],
    minPrice: 0,
    maxPrice: 1000,
    sort: 'newest'
};

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();

    // تحقق مما إذا كان المستخدم مسجل دخول قبل تحميل السلة
    if (document.body.classList.contains('user-logged-in')) {
        loadCartItems();
    }

    // Setup event listeners for both cart buttons
    document.getElementById('closeCart').addEventListener('click', closeCart);

    // Cart toggle in navbar
    document.getElementById('cartToggle')?.addEventListener('click', function() {
        if (!document.body.classList.contains('user-logged-in')) {
            showLoginPrompt('{{ route("login") }}');
            return;
        }

        const cartSidebar = document.getElementById('cartSidebar');
        if (cartSidebar.classList.contains('active')) {
            closeCart();
        } else {
            openCart();
        }
    });

    // Fixed cart button
    document.getElementById('fixedCartBtn')?.addEventListener('click', function() {
        if (!document.body.classList.contains('user-logged-in')) {
            showLoginPrompt('{{ route("login") }}');
            return;
        }

        const cartSidebar = document.getElementById('cartSidebar');
        if (cartSidebar.classList.contains('active')) {
            closeCart();
        } else {
            openCart();
        }
    });

    document.querySelector('.cart-overlay')?.addEventListener('click', closeCart);

    // Setup quick add to cart buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            quickAddToCart(this.dataset.productId);
        });
    });
});

// Initialize Filters
function initializeFilters() {
    // Initialize price range slider with debounce
    const priceRange = document.getElementById('priceRange');
    const priceValue = document.getElementById('priceValue');
    let priceUpdateTimeout;

    if (priceRange) {
        priceRange.addEventListener('input', function() {
            // Update display value immediately
            priceValue.textContent = Number(this.value).toLocaleString() + ' ر.س';

            // Update filter with debounce
            clearTimeout(priceUpdateTimeout);
            priceUpdateTimeout = setTimeout(() => {
                activeFilters.maxPrice = Number(this.value);
                applyFilters();
            }, 500);
        });

        // Add touchend/mouseup event
        priceRange.addEventListener('change', function() {
            clearTimeout(priceUpdateTimeout);
            activeFilters.maxPrice = Number(this.value);
            applyFilters();
        });
    }

    // Category filter handlers
    document.querySelectorAll('.form-check-input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categorySlug = this.value;
            if (this.checked) {
                if (!activeFilters.categories.includes(categorySlug)) {
                    activeFilters.categories.push(categorySlug);
                }
            } else {
                activeFilters.categories = activeFilters.categories.filter(slug => slug !== categorySlug);
            }
            applyFilters();
        });
    });

    // Sort handler
    document.getElementById('sortSelect').addEventListener('change', function() {
        activeFilters.sort = this.value;
        applyFilters();
    });
}

// Apply Filters
function applyFilters() {
    // Show loading state
    const productGrid = document.getElementById('productGrid');
    productGrid.style.opacity = '0.5';

    // Create a copy of activeFilters
    const filterData = {
        categories: activeFilters.categories,
        minPrice: Number(activeFilters.minPrice),
        maxPrice: Number(activeFilters.maxPrice),
        sort: activeFilters.sort
    };

    fetch(window.appConfig.routes.products.filter, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(filterData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success === false) {
            throw new Error(data.message || 'حدث خطأ أثناء تحديث المنتجات');
        }

        // تحديث شبكة المنتجات
        updateProductGrid(data.products || []);

        // تحديث الترقيم الصفحي إذا كان موجوداً
        if (data.links) {
            updatePagination(data.links);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'حدث خطأ أثناء تحديث المنتجات', 'error');
        // Show empty state when error occurs
        updateProductGrid([]);
    })
    .finally(() => {
        productGrid.style.opacity = '1';
    });
}

// Update Product Grid
function updateProductGrid(products) {
    const productGrid = document.getElementById('productGrid');
    productGrid.innerHTML = '';

    if (!products || products.length === 0) {
        productGrid.innerHTML = `
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                <h3>لا توجد منتجات</h3>
                <p class="text-muted">لم يتم العثور على منتجات تطابق معايير البحث</p>
                <button onclick="resetFilters()" class="btn btn-primary mt-3">
                    <i class="fas fa-sync-alt me-2"></i>
                    إعادة ضبط الفلتر
                </button>
            </div>
        `;
        return;
    }

    products.forEach(product => {
        const productElement = document.createElement('div');
        productElement.className = 'col-md-6 col-lg-4';
        productElement.innerHTML = `
            <div class="product-card">
                <a href="/products/${product.slug}" class="product-image-wrapper">
                    <img src="${product.image_url || '/storage/' + product.images[0]?.image_path}"
                         alt="${product.name}"
                         class="product-image">
                </a>
                <div class="product-details">
                    <div class="product-category">${product.category?.name || product.category}</div>
                    <a href="/products/${product.slug}" class="product-title text-decoration-none">
                        <h3>${product.name}</h3>
                    </a>
                    <div class="product-rating">
                        <div class="stars" style="--rating: ${product.rating || 0}"></div>
                        <span class="reviews">(${product.reviews || 0} تقييم)</span>
                    </div>
                    <p class="product-price">${product.price} ر.س</p>
                    <div class="product-actions">
                        <a href="/products/${product.slug}" class="order-product-btn">
                            <i class="fas fa-shopping-cart me-2"></i>
                            طلب المنتج
                        </a>
                    </div>
                </div>
            </div>
        `;
        productGrid.appendChild(productElement);
    });
}

// تحديث الترقيم الصفحي
function updatePagination(links) {
    const paginationContainer = document.querySelector('.pagination');
    if (!paginationContainer) return;

    paginationContainer.innerHTML = '';

    // إضافة زر السابق
    if (links.prev) {
        paginationContainer.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadPage('${links.prev}'); return false;">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }

    // إضافة الأرقام
    if (links.links) {
        links.links.forEach(link => {
            if (link.url === null) return;

            paginationContainer.innerHTML += `
                <li class="page-item ${link.active ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadPage('${link.url}'); return false;">
                        ${link.label}
                    </a>
                </li>
            `;
        });
    }

    // إضافة زر التالي
    if (links.next) {
        paginationContainer.innerHTML += `
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadPage('${links.next}'); return false;">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;
    }
}

// تحميل صفحة معينة
function loadPage(url) {
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success === false) {
            throw new Error(data.message || 'حدث خطأ أثناء تحميل الصفحة');
        }
        updateProductGrid(data.products || []);
        if (data.links) {
            updatePagination(data.links);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ أثناء تحميل الصفحة', 'error');
    });
}

// Reset Filters
function resetFilters() {
    // Reset checkboxes
    document.querySelectorAll('.form-check-input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Reset price range
    const priceRangeInput = document.getElementById('priceRange');
    if (priceRangeInput) {
        priceRangeInput.value = priceRangeInput.max;
        document.getElementById('priceValue').textContent = Number(priceRangeInput.max).toLocaleString() + ' ر.س';
    }

    // Reset sort
    document.getElementById('sortSelect').value = 'newest';

    // Reset active filters
    activeFilters = {
        categories: [],
        minPrice: Number(priceRangeInput?.min || 0),
        maxPrice: Number(priceRangeInput?.max || 1000),
        sort: 'newest'
    };

    // Clear URL parameters
    const url = new URL(window.location.href);
    url.searchParams.delete('category');
    url.searchParams.delete('sort');
    url.searchParams.delete('min_price');
    url.searchParams.delete('max_price');
    window.history.replaceState({}, '', url.toString());

    // Show notification
    showNotification('تم إعادة تعيين الفلتر بنجاح', 'success');

    // Apply reset filters
    applyFilters();
}


function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} notification-toast`;
    notification.innerHTML = message;
    document.body.appendChild(notification);

    // تأثير ظهور الإشعار
    setTimeout(() => notification.classList.add('show'), 100);

    // إخفاء وإزالة الإشعار
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function updateCartDisplay(data) {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCountElements = document.querySelectorAll('.cart-count');

    // تحديث عدد العناصر في كل أزرار السلة
    cartCountElements.forEach(element => {
        element.textContent = data.count || data.cart_count;
    });

    // تحديث الإجمالي
    cartTotal.textContent = (data.total || data.cart_total) + ' ر.س';

    // إذا كانت البيانات تحتوي على items، قم بتحديث قائمة العناصر
    if (data.items && cartItems) {
        cartItems.innerHTML = '';

        if (data.items.length === 0) {
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
            const itemHtml = `
                <div class="cart-item" data-item-id="${item.id}">
                    <div class="cart-item-inner">
                        <button class="remove-btn" onclick="removeFromCart(this, ${item.id})" title="حذف من السلة">
                            <i class="fas fa-times"></i>
                        </button>
                        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                        <div class="cart-item-details">
                            <h5 class="cart-item-title">${item.name}</h5>
                            <div class="cart-item-price">${item.price} ر.س</div>
                            <div class="quantity-controls">
                                <button class="btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                                <input type="number"
                                       value="${item.quantity}"
                                       min="1"
                                       class="quantity-input"
                                       onchange="updateQuantity(${item.id}, 0, this.value)">
                                <button class="btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                            </div>
                            <div class="cart-item-subtotal">
                                الإجمالي: ${item.subtotal} ر.س
                            </div>
                        </div>
                    </div>
                </div>
            `;
            cartItems.insertAdjacentHTML('beforeend', itemHtml);
        });
    }
}

function updateQuantity(itemId, change, newValue = null) {
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
            // تحديث الكمية والإجمالي الفرعي للعنصر فقط
            quantityInput.value = quantity;
            const subtotalElement = cartItem.querySelector('.cart-item-subtotal');
            subtotalElement.textContent = `الإجمالي: ${data.item_subtotal} ر.س`;

            // تحديث إجمالي السلة وعدد العناصر
            const cartTotal = document.getElementById('cartTotal');
            const cartCountElements = document.querySelectorAll('.cart-count');

            cartTotal.textContent = data.cart_total + ' ر.س';
            cartCountElements.forEach(element => {
                element.textContent = data.cart_count;
            });
        } else {
            // إرجاع القيمة القديمة في حالة الخطأ
            quantityInput.value = currentValue;
            showNotification(data.message || 'فشل تحديث الكمية', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // إرجاع القيمة القديمة في حالة الخطأ
        quantityInput.value = currentValue;
        showNotification('حدث خطأ أثناء تحديث الكمية', 'error');
    })
    .finally(() => {
        cartItem.style.opacity = '1';
    });
}

function removeFromCart(button, cartItemId) {
    event.preventDefault();

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
        .then(data => updateCartDisplay(data))
        .catch(error => console.error('Error:', error));
}

function showLoginPrompt(loginUrl) {
    const currentUrl = window.location.href;
    const modal = new bootstrap.Modal(document.getElementById('loginPromptModal'));
    document.getElementById('loginButton').href = `${loginUrl}?redirect=${encodeURIComponent(currentUrl)}`;
    modal.show();
}
