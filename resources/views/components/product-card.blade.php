@php
use Illuminate\Support\Facades\Storage;
@endphp

@props(['product'])

<div class="product-card">
    <div class="image-container">
        @if($product->primary_image)
            <img src="{{ Storage::url($product->primary_image->image_path) }}"
                alt="{{ $product->name }}"
                class="product-image">
        @else
            <div class="flex items-center justify-center h-full bg-gray-100">
                <svg class="w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif
    </div>

    <div class="product-info">
        <h3 class="product-name">
            <a href="{{ route('products.show', $product) }}" class="hover:text-primary-600">
                {{ $product->name }}
            </a>
        </h3>
        <p class="product-category">{{ $product->category->name }}</p>

        <div class="flex items-center justify-between mt-4">
            <span class="price-tag">{{ number_format($product->price / 100, 2) }} SAR</span>
            @if($product->stock <= 5 && $product->stock > 0)
                <span class="stock-status low-stock">
                    {{ $product->stock }} left
                </span>
            @elseif($product->stock === 0)
                <span class="stock-status out-of-stock">
                    Out of stock
                </span>
            @endif
        </div>

        @if($product->stock > 0)
            <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn-primary w-full flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Add to Cart
                </button>
            </form>
        @endif
    </div>
</div>
