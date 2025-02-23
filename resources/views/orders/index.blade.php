@extends('layouts.customer')

@section('title', 'طلباتي')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('assets/css/customer/orders.css') }}">
@endsection

@section('content')
<header class="header-container">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="page-title">طلباتي</h2>
                <p class="page-subtitle">إدارة وتتبع طلباتك</p>
            </div>
            <div class="col-md-6 text-start">
                <a href="/products" class="btn btn-outline-primary me-2">
                    <i class="bi bi-cart"></i>
                    متابعة التسوق
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="bi bi-printer"></i>
                    طباعة الطلبات
                </button>
            </div>
        </div>
    </div>
</header>

<main class="container py-4">
    @forelse($orders as $order)
    <div class="order-card">
        <div class="order-header">
            <div class="d-flex align-items-center">
                <div class="order-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="ms-3">
                    <h3 class="order-number">طلب #{{ $order->order_number }}</h3>
                    <span class="status-badge status-{{ $order->order_status }}">
                        {{ match($order->order_status) {
                            'completed' => 'مكتمل',
                            'cancelled' => 'ملغي',
                            'processing' => 'قيد المعالجة',
                            'pending' => 'قيد الانتظار',
                            'out_for_delivery' => 'جاري التوصيل',
                            'on_the_way' => 'في الطريق',
                            'delivered' => 'تم التوصيل',
                            'returned' => 'مرتجع',
                            default => 'غير معروف'
                        } }}
                    </span>
                </div>
            </div>
            <div class="order-meta">
                <div class="order-date">
                    <i class="bi bi-calendar"></i>
                    {{ $order->created_at->format('Y/m/d') }}
                </div>
                <div class="order-total">
                    {{ $order->total_amount }} ريال
                </div>
                <a href="{{ route('orders.show', $order->uuid) }}" class="btn btn-primary">
                    <i class="bi bi-eye"></i>
                    عرض التفاصيل
                </a>
            </div>
        </div>

        <div class="order-details">
            <div class="row">
                @foreach($order->items->take(4) as $item)
                <div class="col-md-3">
                    <div class="order-item">
                        @if($item->product->images->first())
                        <img src="{{ Storage::url($item->product->images->first()->image_path) }}"
                            alt="{{ $item->product->name }}"
                            class="item-image">
                        @endif
                        <div class="item-details">
                            <h4 class="item-name">{{ $item->product->name }}</h4>
                            <p class="item-price">
                                الكمية: {{ $item->quantity }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($order->items->count() > 4)
            <div class="text-center mt-3">
                <a href="{{ route('orders.show', $order->uuid) }}" class="btn btn-link">
                    عرض {{ $order->items->count() - 4 }} منتجات إضافية
                </a>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="bi bi-box"></i>
        </div>
        <h3>لا توجد طلبات حتى الآن</h3>
        <p>سجل طلباتك فارغ. ابدأ التسوق لاكتشاف منتجاتنا المميزة!</p>
        <a href="/products" class="btn btn-primary">
            ابدأ التسوق الآن
        </a>
    </div>
    @endforelse

    <div class="mt-4">
        @if($orders->hasPages())
            <nav aria-label="صفحات الطلبات">
                <div class="pagination">
                    {{-- Previous Page Link --}}
                    @if($orders->onFirstPage())
                        <span class="page-item disabled">
                            <span class="page-link" aria-hidden="true">
                                <i class="bi bi-chevron-right"></i>
                            </span>
                        </span>
                    @else
                        <span class="page-item">
                            <a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </span>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if($page == $orders->currentPage())
                            <span class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </span>
                        @else
                            <span class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </span>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if($orders->hasMorePages())
                        <span class="page-item">
                            <a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </span>
                    @else
                        <span class="page-item disabled">
                            <span class="page-link" aria-hidden="true">
                                <i class="bi bi-chevron-left"></i>
                            </span>
                        </span>
                    @endif
                </div>
            </nav>
        @endif
    </div>
</main>
@endsection
