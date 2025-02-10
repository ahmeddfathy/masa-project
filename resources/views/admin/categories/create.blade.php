@extends('layouts.admin')

@section('title', 'إضافة تصنيف جديد')
@section('page_title', 'إضافة تصنيف جديد')

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
                                            <i class="fas fa-plus text-primary me-2"></i>
                                            إضافة تصنيف جديد
                                        </h5>
                                        <div class="actions">
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
                        <form action="{{ route('admin.categories.store') }}" method="POST">
                            @csrf

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
                                                               value="{{ old('name') }}">
                                                        @error('name')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">الوصف</label>
                                                        <textarea name="description" class="form-control shadow-sm" rows="4">{{ old('description') }}</textarea>
                                                        @error('description')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
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
                                                حفظ التصنيف
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
<link rel="stylesheet" href="{{ asset('assets/css/admin/category.css') }}">
@endsection
