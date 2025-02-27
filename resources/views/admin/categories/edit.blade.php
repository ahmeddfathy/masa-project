@extends('layouts.admin')

@section('title', 'تعديل التصنيف - ' . $category->name)
@section('page_title', 'تعديل التصنيف: ' . $category->name)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="categories-container">
                        <!-- Header Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-edit text-primary me-2"></i>
                                            تعديل التصنيف
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-light-info me-2">
                                                <i class="fas fa-eye me-1"></i>
                                                عرض التصنيف
                                            </a>
                                            <a href="{{ route('admin.categories.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للتصنيفات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form -->
                        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                معلومات التصنيف
                                            </h5>

                                            <div class="row g-4">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">اسم التصنيف</label>
                                                        <input type="text" name="name" class="form-control shadow-sm"
                                                               value="{{ old('name', $category->name) }}">
                                                        @error('name')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">الوصف</label>
                                                        <textarea name="description" class="form-control shadow-sm" rows="4">{{ old('description', $category->description) }}</textarea>
                                                        @error('description')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Category Stats -->
                                <div class="col-12 mt-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-chart-bar text-primary me-2"></i>
                                                إحصائيات التصنيف
                                            </h5>

                                            <div class="row g-4">
                                                <div class="col-md-4">
                                                    <div class="stat-card bg-primary bg-opacity-10 rounded p-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-box-open text-primary fa-2x me-3"></i>
                                                            <div>
                                                                <h6 class="mb-1">عدد المنتجات</h6>
                                                                <h4 class="mb-0 text-primary">{{ $category->products_count }}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="stat-card bg-success bg-opacity-10 rounded p-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-clock text-success fa-2x me-3"></i>
                                                            <div>
                                                                <h6 class="mb-1">تاريخ الإنشاء</h6>
                                                                <h4 class="mb-0 text-success">{{ $category->created_at->format('Y/m/d') }}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="stat-card bg-info bg-opacity-10 rounded p-3">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-calendar text-info fa-2x me-3"></i>
                                                            <div>
                                                                <h6 class="mb-1">آخر تحديث</h6>
                                                                <h4 class="mb-0 text-info">{{ $category->updated_at->format('Y/m/d') }}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12 mt-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                حفظ التغييرات
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
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
