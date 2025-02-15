@extends('layouts.admin')

@section('title', $product->name)
@section('page_title', $product->name)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="products-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-box text-primary me-2"></i>
                                            تفاصيل المنتج
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-light-primary me-2">
                                                <i class="fas fa-edit me-1"></i>
                                                تعديل المنتج
                                            </a>
                                            <a href="{{ route('admin.products.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للمنتجات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <!-- Product Images -->
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-images text-primary me-2"></i>
                                            صور المنتج
                                        </h5>
                                        <img src="{{ Storage::url($product->primary_image->image_path) }}"
                                             alt="{{ $product->name }}"
                                             class="product-image mb-3"
                                             id="mainImage">

                                        @if($product->images->count() > 1)
                                        <div class="d-flex gap-2 flex-wrap">
                                            @foreach($product->images as $image)
                                            <img src="{{ Storage::url($image->image_path) }}"
                                                 alt="صورة المنتج"
                                                 class="thumbnail {{ $image->is_primary ? 'active' : '' }}"
                                                 onclick="updateMainImage(this, '{{ Storage::url($image->image_path) }}')">
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Product Details -->
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            معلومات المنتج
                                        </h5>
                                        <div class="mb-4">
                                            <span class="status-badge {{ $product->stock > 10 ? 'in-stock' : ($product->stock > 0 ? 'low-stock' : 'out-of-stock') }}">
                                                @if($product->stock > 10)
                                                    متوفر ({{ $product->stock }})
                                                @elseif($product->stock > 0)
                                                    مخزون منخفض ({{ $product->stock }})
                                                @else
                                                    غير متوفر
                                                @endif
                                            </span>
                                        </div>
                                        <div class="row g-4">
                                            <div class="col-6">
                                                <div class="detail-item">
                                                    <dt><i class="fas fa-tag text-primary"></i> التصنيف</dt>
                                                    <dd>{{ $product->category->name }}</dd>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="detail-item">
                                                    <dt><i class="fas fa-money-bill text-primary"></i> السعر</dt>
                                                    <dd class="text-primary fw-bold">{{ number_format($product->price, 0) }} ريال</dd>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="detail-item">
                                                    <dt><i class="fas fa-align-left text-primary"></i> الوصف</dt>
                                                    <dd>{{ $product->description }}</dd>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Colors -->
                            @if($product->colors && $product->colors->isNotEmpty())
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-palette text-primary me-2"></i>
                                            الألوان المتاحة
                                        </h5>
                                        <div class="row g-3">
                                            @foreach($product->colors as $color)
                                            <div class="col-md-6">
                                                <div class="color-item d-flex align-items-center p-3 rounded border">
                                                    <span class="color-preview me-2" style="width: 20px; height: 20px; border-radius: 50%; background-color: {{ $color->color }}"></span>
                                                    <span>{{ $color->color }}</span>
                                                    <span class="ms-auto">
                                                        @if($color->is_available)
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Sizes -->
                            @if($product->sizes && $product->sizes->isNotEmpty())
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-ruler text-primary me-2"></i>
                                            المقاسات المتاحة
                                        </h5>
                                        <div class="row g-3">
                                            @foreach($product->sizes as $size)
                                            <div class="col-md-6">
                                                <div class="size-item d-flex align-items-center p-3 rounded border">
                                                    <span>{{ $size->size }}</span>
                                                    <span class="ms-auto">
                                                        @if($size->is_available)
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Product Options -->
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">
                                            <i class="fas fa-cog text-primary me-2"></i>
                                            خيارات المنتج
                                        </h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="option-item d-flex align-items-center p-3 rounded border">
                                                    <i class="fas fa-calendar-check text-primary me-2"></i>
                                                    <span>حجز موعد للمقاسات</span>
                                                    <span class="ms-auto">
                                                        @if($product->enable_appointments)
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="option-item d-flex align-items-center p-3 rounded border">
                                                    <i class="fas fa-palette text-primary me-2"></i>
                                                    <span>اختيار الألوان المحددة</span>
                                                    <span class="ms-auto">
                                                        @if($product->enable_color_selection)
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="option-item d-flex align-items-center p-3 rounded border">
                                                    <i class="fas fa-paint-brush text-primary me-2"></i>
                                                    <span>إضافة لون مخصص</span>
                                                    <span class="ms-auto">
                                                        @if($product->enable_custom_color)
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="option-item d-flex align-items-center p-3 rounded border">
                                                    <i class="fas fa-ruler text-primary me-2"></i>
                                                    <span>اختيار المقاسات المحددة</span>
                                                    <span class="ms-auto">
                                                        @if($product->enable_size_selection)
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="option-item d-flex align-items-center p-3 rounded border">
                                                    <i class="fas fa-ruler-combined text-primary me-2"></i>
                                                    <span>إضافة مقاس مخصص</span>
                                                    <span class="ms-auto">
                                                        @if($product->enable_custom_size)
                                                            <i class="fas fa-check text-success"></i>
                                                        @else
                                                            <i class="fas fa-times text-danger"></i>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.products-container {
    padding: 1.5rem;
    width: 100%;
}

.product-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 0.5rem;
}

.thumbnail {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 0.5rem;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.thumbnail.active {
    border-color: var(--primary);
}

.detail-item {
    margin-bottom: 1rem;
}

.detail-item dt {
    font-size: 0.875rem;
    color: var(--text-medium);
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.detail-item dd {
    font-size: 1rem;
    margin: 0;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge.in-stock {
    background: #ECFDF5;
    color: var(--success);
}

.status-badge.low-stock {
    background: #FFFBEB;
    color: var(--warning);
}

.status-badge.out-of-stock {
    background: #FEF2F2;
    color: var(--danger);
}

.color-item,
.size-item {
    background: white;
    transition: all 0.3s ease;
}

.color-item:hover,
.size-item:hover {
    background: #f8f9fa;
}

@media (max-width: 768px) {
    .products-container {
        padding: 0.75rem;
    }

    .product-image {
        height: 300px;
    }

    .thumbnail {
        width: 60px;
        height: 60px;
    }
}
</style>
@endsection

@section('scripts')
<script>
function updateMainImage(thumbnail, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    thumbnail.classList.add('active');
}
</script>
@endsection
