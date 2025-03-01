@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('admin.addons.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium mb-3">اسم الإضافة</label>
                            <input type="text" class="form-control form-control-lg rounded-4 @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium mb-3">وصف الإضافة</label>
                            <textarea class="form-control form-control-lg rounded-4 @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-medium mb-3">السعر الأساسي</label>
                                <input type="number" step="0.01" class="form-control form-control-lg rounded-4 @error('price') is-invalid @enderror"
                                       id="price" name="price" value="{{ old('price') }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium mb-3">الباقات المتاحة</label>
                            <div class="row g-3">
                                @foreach($packages as $package)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check package-item p-3 border rounded">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   name="package_ids[]"
                                                   id="package_{{ $package->id }}"
                                                   value="{{ $package->id }}"
                                                   {{ in_array($package->id, old('package_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label d-block" for="package_{{ $package->id }}">
                                                <strong>{{ $package->name }}</strong>
                                                <div class="mt-2">
                                                    <small class="text-muted">الخدمات:
                                                        @foreach($package->services as $service)
                                                            <span class="badge bg-success me-1">{{ $service->name }}</span>
                                                        @endforeach
                                                    </small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('package_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active"
                                       name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="is_active">نشط</label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-3">حفظ</button>
                            <a href="{{ route('admin.addons.index') }}" class="btn btn-secondary btn-lg px-5 rounded-3 ms-2">رجوع</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="/assets/css/admin/addons.css">
    <style>
        .form-control {
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        .card {
            background: #fff;
            border-radius: 1rem;
        }
        .btn {
            padding: 0.75rem 2rem;
            font-weight: 500;
        }
        .form-check-input {
            width: 1.2em;
            height: 1.2em;
            margin-top: 0.25em;
            cursor: pointer;
        }
        .form-check-label {
            cursor: pointer;
            padding-right: 0.5rem;
        }
        .package-item {
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        .package-item:hover {
            background-color: #e9ecef;
            border-color: #3b82f6 !important;
        }
        .form-check-input:checked + .form-check-label .badge {
            background-color: #3b82f6 !important;
        }
    </style>
@endsection
