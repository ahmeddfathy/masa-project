@extends('layouts.admin')

@section('title', 'التصنيفات')
@section('page_title', 'إدارة التصنيفات')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="categories-container">
                        <!-- Stats Cards -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-primary me-3">
                                                <i class="fas fa-layer-group fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">إجمالي التصنيفات</h6>
                                                <h3 class="text-white mb-0">{{ $categories->total() }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-success h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-success me-3">
                                                <i class="fas fa-box-open fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">إجمالي المنتجات</h6>
                                                <h3 class="text-white mb-0">{{ $categories->sum('products_count') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm stat-card bg-gradient-info h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-white text-info me-3">
                                                <i class="fas fa-clock fa-lg"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-white mb-1">آخر تحديث</h6>
                                                <h3 class="text-white mb-0">{{ $categories->first()?->updated_at->format('Y/m/d') ?? 'لا يوجد' }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="card-title mb-1 d-flex align-items-center">
                                                <span class="icon-circle bg-primary text-white me-2">
                                                    <i class="fas fa-tags"></i>
                                                </span>
                                                إدارة التصنيفات
                                            </h5>
                                            <p class="text-muted mb-0 fs-sm">إدارة وتنظيم تصنيفات المنتجات</p>
                                        </div>
                                        <div class="actions">
                                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-wave">
                                                <i class="fas fa-plus me-2"></i>
                                                إضافة تصنيف جديد
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <form action="{{ route('admin.categories.index') }}" method="GET">
                                            <div class="row g-3">
                                                <div class="col-md-10">
                                                    <div class="search-wrapper">
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light border-0">
                                                                <i class="fas fa-search text-muted"></i>
                                                            </span>
                                                            <input type="text" name="search" class="form-control border-0 shadow-none ps-0"
                                                                placeholder="البحث في التصنيفات..."
                                                                value="{{ request('search') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary btn-wave w-100">
                                                        <i class="fas fa-search me-2"></i>
                                                        بحث
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Categories Grid -->
                        <div class="row g-4">
                            @forelse($categories as $category)
                            <div class="col-md-6">
                                <div class="card border-0 shadow-hover category-card h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title h4 mb-2">{{ $category->name }}</h5>
                                                <p class="text-muted description mb-0">
                                                    {{ Str::limit($category->description, 100) ?: 'لا يوجد وصف' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                                    <i class="fas fa-box-open me-1"></i>
                                                    {{ $category->products_count }} منتج
                                                </span>
                                                <span class="text-muted small">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $category->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <div class="action-buttons">
                                                <a href="{{ route('admin.categories.show', $category) }}"
                                                   class="btn btn-light-info btn-sm me-2"
                                                   title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.categories.edit', $category) }}"
                                                   class="btn btn-light-primary btn-sm me-2"
                                                   title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.categories.destroy', $category) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn btn-light-danger btn-sm"
                                                            title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-icon bg-light rounded-circle mb-3">
                                                <i class="fas fa-folder-open text-muted fa-2x"></i>
                                            </div>
                                            <h5 class="text-muted mb-3">لا توجد تصنيفات</h5>
                                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-wave">
                                                <i class="fas fa-plus me-2"></i>
                                                إضافة أول تصنيف
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        @if($categories->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $categories->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="/assets/css/admin/category.css">
@endsection
