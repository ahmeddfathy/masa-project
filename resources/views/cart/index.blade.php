@extends('layouts.customer')

@section('title', 'سلة التسوق')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('assets/css/customer/cart.css') }}">
@endsection

@section('content')
<div class="container py-4">
  @if (session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  @if (session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="page-title mb-0">سلة التسوق</h2>
    <span class="text-muted">{{ $cart_items_count ?? 0 }} منتجات</span>
  </div>

  <div class="cart-container">
    @if(isset($cart_items) && count($cart_items) > 0)
    <div class="row">
      <div class="col-lg-8">
        @foreach($cart_items as $item)
        <div class="cart-item d-flex gap-3">
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
              <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="remove-item">
                  <i class="bi bi-x-circle"></i>
                </button>
              </form>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="quantity-control">
                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline update-quantity-form">
                  @csrf
                  @method('PATCH')
                  <button type="button" class="quantity-btn increase">
                    <i class="bi bi-plus"></i>
                  </button>
                  <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                  class="quantity-input" >
                  <button type="button" class="quantity-btn decrease">
                    <i class="bi bi-dash"></i>
                  </button>
                </form>
              </div>
              <div class="cart-item-price">
                {{ $item->product->price * $item->quantity }} ريال
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
            <span class="summary-value">{{ $subtotal }} ريال</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">الإجمالي الكلي</span>
            <span class="total-amount">{{ $total }} ريال</span>
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
  document.addEventListener('DOMContentLoaded', function() {
    // التحكم في الكمية
    document.querySelectorAll('.quantity-control').forEach(control => {
      const input = control.querySelector('.quantity-input');
      const form = control.querySelector('.update-quantity-form');
      const decreaseBtn = control.querySelector('.decrease');
      const increaseBtn = control.querySelector('.increase');

      decreaseBtn.addEventListener('click', () => {
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
          input.value = currentValue - 1;
          form.submit();
        }
      });

      increaseBtn.addEventListener('click', () => {
        input.value = parseInt(input.value) + 1;
        form.submit();
      });
    });

    // إخفاء رسائل التنبيه تلقائياً
    setTimeout(() => {
      document.querySelectorAll('.alert').forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      });
    }, 3000);
  });
</script>
@endsection
