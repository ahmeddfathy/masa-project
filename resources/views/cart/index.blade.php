@extends('layouts.customer')

@section('title', 'سلة التسوق')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('assets/css/customer/cart.css') }}">
@endsection

@section('content')
<div class="container py-4">
  <div id="alerts-container"></div>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">سلة التسوق</h2>
    <span class="text-muted">{{ $cart_items_count ?? 0 }} منتجات</span>
  </div>

  <div class="cart-container">
    @if(isset($cart_items) && count($cart_items) > 0)
    <div class="row">
      <div class="col-lg-8">
        @foreach($cart_items as $item)
        <div class="cart-item d-flex gap-3" data-item-id="{{ $item->id }}">
          @php
          // Get any available image for the product, not just primary
          $productImage = $item->product->images->first();
          $imagePath = $productImage ? Storage::url($productImage->image_path) : asset('images/no-image.png');
          @endphp
          <img src="{{ $imagePath }}" alt="{{ $item->product->name }}" class="cart-item-image">
          <div class="cart-item-details">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <h5 class="cart-item-title">{{ $item->product->name }}</h5>
                <div class="cart-item-meta">
                  @if($item->product->category)
                  <span class="me-2">{{ $item->product->category->name }}</span>
                  @endif
                  @if($item->size)
                  <span class="me-2">المقاس: {{ $item->size }}</span>
                  @endif
                  @if($item->color)
                  <span>اللون: {{ $item->color }}</span>
                  @endif
                </div>
              </div>
              <button type="button" class="remove-item" onclick="removeCartItem({{ $item->id }})">
                <i class="bi bi-x-circle"></i>
              </button>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="quantity-control">
                <button type="button" class="quantity-btn decrease" onclick="updateQuantity({{ $item->id }}, -1)">
                  <i class="bi bi-dash"></i>
                </button>
                <input type="number" value="{{ $item->quantity }}" min="1" class="quantity-input"
                       onchange="updateQuantity({{ $item->id }}, 0, this.value)">
                <button type="button" class="quantity-btn increase" onclick="updateQuantity({{ $item->id }}, 1)">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
              <div class="cart-item-price" id="price-{{ $item->id }}">
                {{ number_format($item->product->price * $item->quantity, 2) }} ريال
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="col-lg-4">
        <div class="cart-summary">
          <h4 class="mb-4">ملخص الطلب</h4>
          <div class="summary-item">
            <span class="summary-label">إجمالي المنتجات</span>
            <span class="summary-value" id="subtotal">{{ number_format($subtotal, 2) }} ريال</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">الإجمالي الكلي</span>
            <span class="total-amount" id="total">{{ number_format($total, 2) }} ريال</span>
          </div>
          <a href="{{ route('checkout.index') }}" class="btn btn-primary checkout-btn">
            متابعة الشراء
          </a>
          <div class="continue-shopping mt-3">
            <a href="{{ route('products.index') }}">
              <i class="bi bi-arrow-right"></i>
              متابعة التسوق
            </a>
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="empty-cart">
      <div class="empty-cart-icon">
        <i class="bi bi-cart-x"></i>
      </div>
      <h3>السلة فارغة</h3>
      <p>لم تقم بإضافة أي منتجات إلى سلة التسوق بعد</p>
      <a href="{{ route('products.index') }}" class="btn btn-primary">
        تصفح المنتجات
      </a>
    </div>
    @endif
  </div>
</div>
@endsection

@section('scripts')
<script>
function showAlert(message, type = 'success') {
    const alertsContainer = document.getElementById('alerts-container');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    alertsContainer.appendChild(alert);

    // Auto-hide after 3 seconds
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }, 3000);
}

function updateQuantity(itemId, change, newValue = null) {
    const input = document.querySelector(`[data-item-id="${itemId}"] .quantity-input`);
    const currentValue = parseInt(input.value);
    let quantity = newValue !== null ? parseInt(newValue) : currentValue + change;

    if (quantity < 1) return;

    const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
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
            // تحديث الكمية
            input.value = quantity;

            // تحديث السعر الفرعي للمنتج
            document.getElementById(`price-${itemId}`).textContent =
                new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' })
                    .format(data.item_subtotal)
                    .replace('SAR', 'ريال');

            // تحديث إجمالي السلة
            document.getElementById('total').textContent =
                new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' })
                    .format(data.cart_total)
                    .replace('SAR', 'ريال');

            showAlert('تم تحديث الكمية بنجاح');
        } else {
            input.value = currentValue;
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        input.value = currentValue;
        showAlert('حدث خطأ أثناء تحديث الكمية', 'danger');
    })
    .finally(() => {
        cartItem.style.opacity = '1';
    });
}

function removeCartItem(itemId) {
    if (!confirm('هل أنت متأكد من حذف هذا المنتج من السلة؟')) {
        return;
    }

    const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
    cartItem.style.opacity = '0.5';

    fetch(`/cart/items/${itemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تأثير حذف المنتج
            cartItem.style.transform = 'translateX(100px)';
            cartItem.style.opacity = '0';

            setTimeout(() => {
                cartItem.remove();

                // تحديث إجمالي السلة
                document.getElementById('total').textContent =
                    new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' })
                        .format(data.cart_total)
                        .replace('SAR', 'ريال');

                // إذا أصبحت السلة فارغة
                if (data.cart_count === 0) {
                    location.reload();
                }
            }, 300);

            showAlert('تم حذف المنتج من السلة بنجاح');
        } else {
            cartItem.style.opacity = '1';
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        cartItem.style.opacity = '1';
        showAlert('حدث خطأ أثناء حذف المنتج', 'danger');
    });
}

// إخفاء رسائل التنبيه تلقائياً عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 3000);
});
</script>
@endsection
