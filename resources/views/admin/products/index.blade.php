@extends('layouts.admin')

@section('title', 'إدارة المنتجات')
@section('page_title', 'إدارة المنتجات')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="products-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">
                                            <i class="fas fa-filter text-primary me-2"></i>
                                            تصفية المنتجات
                                        </h5>
                                        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fas fa-search text-primary me-2"></i>
                                                    بحث
                                                </label>
                                                <input type="text" name="search" class="form-control shadow-sm"
                                                       placeholder="ابحث عن المنتجات..." value="{{ request('search') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fas fa-tag text-primary me-2"></i>
                                                    التصنيف
                                                </label>
                                                <select name="category" class="form-select shadow-sm">
                                                    <option value="">جميع الفئات</option>
                                                    @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">
                                                    <i class="fas fa-sort text-primary me-2"></i>
                                                    الترتيب
                                                </label>
                                                <select name="sort" class="form-select shadow-sm">
                                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>الأحدث أولاً</option>
                                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>الأقدم أولاً</option>
                                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>السعر من الأعلى</option>
                                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>السعر من الأقل</option>
                                                </select>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-filter me-2"></i>
                                                    تصفية
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">
                                            <i class="fas fa-plus text-primary me-2"></i>
                                            إضافة منتج
                                        </h5>
                                        <a href="{{ route('admin.products.create') }}" class="btn btn-success w-100">
                                            <i class="fas fa-plus me-2"></i>
                                            إضافة منتج جديد
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="row g-4">
                                    @forelse($products as $product)
                                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                        <div class="product-card shadow-sm">
                                            <div class="product-image-container">
                                                @if($product->primary_image)
                                                <img src="{{ url('storage/' . $product->primary_image->image_path) }}"
                                                    alt="{{ $product->name }}"
                                                    class="product-image" />
                                                @else
                                                <div class="no-image">
                                                    <i class="fas fa-image"></i>
                                                    <span>لا توجد صورة</span>
                                                </div>
                                                @endif

                                                <span class="stock-badge {{ $product->stock > 10 ? 'in-stock' : ($product->stock > 0 ? 'low-stock' : 'out-of-stock') }}">
                                                    @if($product->stock > 10)
                                                        <i class="fas fa-check-circle"></i>
                                                    @elseif($product->stock > 0)
                                                        <i class="fas fa-exclamation-circle"></i>
                                                    @else
                                                        <i class="fas fa-times-circle"></i>
                                                    @endif
                                                    {{ $product->stock > 0 ? $product->stock . ' في المخزون' : 'نفذت الكمية' }}
                                                </span>
                                            </div>

                                            <div class="product-details p-3">
                                                <span class="category-badge">
                                                    <i class="fas fa-tag"></i>
                                                    {{ $product->category->name }}
                                                </span>
                                                <h5 class="product-title mt-2">{{ $product->name }}</h5>
                                                <p class="product-description text-muted">
                                                    {{ Str::limit($product->description, 100) }}
                                                </p>
                                                <div class="product-price fw-bold text-primary mt-2">
                                                    @if($product->min_price == $product->max_price)
                                                        {{ number_format($product->min_price, 0) }} ريال
                                                    @else
                                                        {{ number_format($product->min_price, 0) }} - {{ number_format($product->max_price, 0) }} ريال
                                                    @endif
                                                </div>
                                                <div class="product-status mt-2">
                                                    @if($product->is_available)
                                                        <span class="badge bg-success">متاح للبيع</span>
                                                    @else
                                                        <span class="badge bg-danger">غير متاح</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="card-footer bg-light border-top p-3">
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.products.show', $product) }}"
                                                       class="btn btn-sm btn-light-info flex-grow-1">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.products.edit', $product) }}"
                                                       class="btn btn-sm btn-light-primary flex-grow-1">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.products.destroy', $product) }}"
                                                          method="POST"
                                                          class="flex-grow-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-light-danger w-100"
                                                                onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="col-12">
                                        <div class="empty-state text-center py-5">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <h4>لا توجد منتجات</h4>
                                            <p class="text-muted">لم يتم العثور على أي منتجات. يمكنك إضافة منتج جديد من خلال الزر أعلاه.</p>
                                        </div>
                                    </div>
                                    @endforelse
                                </div>

                                @if($products->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $products->links() }}
                                </div>
                                @endif
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
<link rel="stylesheet" href="{{ asset('assets/css/admin/products.css') }}?t={{ time() }}">
@endsection
