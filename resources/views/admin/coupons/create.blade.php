@extends('layouts.admin')

@section('title', 'إضافة كوبون جديد')
@section('page_title', 'إضافة كوبون جديد')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid px-0">
            <div class="row mx-0">
                <div class="col-12 px-0">
                    <div class="coupons-container">
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-plus text-primary me-2"></i>
                                            إضافة كوبون جديد
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للكوبونات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.coupons.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                معلومات الكوبون الأساسية
                                            </h5>

                                            <div class="row g-4">
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">اسم الكوبون</label>
                                                        <input type="text" name="name" class="form-control shadow-sm" value="{{ old('name') }}" required>
                                                        @error('name')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">كود الكوبون</label>
                                                        <div class="input-group">
                                                            <input type="text" name="code" id="coupon_code" class="form-control shadow-sm" value="{{ old('code') }}" required>
                                                            <button type="button" id="generate_coupon" class="btn btn-light-primary">
                                                                <i class="fas fa-sync-alt me-1"></i>
                                                                توليد كود
                                                            </button>
                                                        </div>
                                                        @error('code')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                        <span class="text-muted small">سيتم تحويل الكود إلى أحرف كبيرة تلقائياً</span>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">نوع الخصم</label>
                                                        <select name="type" id="type" class="form-select shadow-sm">
                                                            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>نسبة مئوية (%)</option>
                                                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>مبلغ ثابت (ر.س)</option>
                                                        </select>
                                                        @error('type')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">قيمة الخصم</label>
                                                        <input type="number" name="value" id="value" class="form-control shadow-sm" step="0.01" min="0" value="{{ old('value') }}" required>
                                                        @error('value')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                        <span id="value-help" class="text-muted small">
                                                            أدخل النسبة المئوية بدون علامة % (مثال: 10 للحصول على خصم 10%)
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">وصف الكوبون</label>
                                                        <textarea name="description" rows="3" class="form-control shadow-sm">{{ old('description') }}</textarea>
                                                        @error('description')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-cog text-primary me-2"></i>
                                                إعدادات الاستخدام
                                            </h5>

                                            <div class="row g-4">
                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">الحد الأدنى لقيمة الطلب</label>
                                                        <input type="number" name="min_order_amount" class="form-control shadow-sm" step="0.01" min="0" value="{{ old('min_order_amount', 0) }}" required>
                                                        @error('min_order_amount')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">الحد الأقصى للاستخدام</label>
                                                        <input type="number" name="max_uses" class="form-control shadow-sm" min="1" value="{{ old('max_uses') }}">
                                                        @error('max_uses')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                        <span class="text-muted small">اتركه فارغاً لعدد غير محدود من الاستخدامات</span>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">تاريخ البدء</label>
                                                        <input type="datetime-local" name="starts_at" class="form-control shadow-sm" value="{{ old('starts_at') }}">
                                                        @error('starts_at')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                        <span class="text-muted small">اتركه فارغاً للبدء فوراً</span>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">تاريخ الانتهاء</label>
                                                        <input type="datetime-local" name="expires_at" class="form-control shadow-sm" value="{{ old('expires_at') }}">
                                                        @error('expires_at')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                        <span class="text-muted small">اتركه فارغاً لكوبون بدون تاريخ انتهاء</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-link text-primary me-2"></i>
                                                تطبيق الكوبون
                                            </h5>

                                            <div class="row g-4">
                                                <div class="col-12 mb-3">
                                                    <h6 class="form-label mb-3">نوع الكوبون</h6>
                                                    <div class="mb-3 d-flex flex-wrap gap-3">
                                                        <div class="coupon-type-card border rounded-3 p-3 position-relative">
                                                            <input class="form-check-input position-absolute top-0 end-0 m-2" type="checkbox" id="applies_to_products" name="applies_to_products" value="1" {{ old('applies_to_products', 0) ? 'checked' : '' }}>
                                                            <div class="mb-2 text-center">
                                                                <i class="fas fa-shopping-bag fa-2x text-primary"></i>
                                                            </div>
                                                            <label class="form-check-label d-block text-center" for="applies_to_products">ينطبق على المنتجات</label>
                                                        </div>
                                                        <div class="coupon-type-card border rounded-3 p-3 position-relative">
                                                            <input class="form-check-input position-absolute top-0 end-0 m-2" type="checkbox" id="applies_to_packages" name="applies_to_packages" value="1" {{ old('applies_to_packages', 0) ? 'checked' : '' }}>
                                                            <div class="mb-2 text-center">
                                                                <i class="fas fa-box fa-2x text-info"></i>
                                                            </div>
                                                            <label class="form-check-label d-block text-center" for="applies_to_packages">ينطبق على الباقات</label>
                                                        </div>
                                                    </div>
                                                    @error('applies_to_products')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                    @error('applies_to_packages')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-12 mb-3" id="products-container">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="applies_to_all_products" name="applies_to_all_products" value="1" {{ old('applies_to_all_products', 0) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="applies_to_all_products">ينطبق على جميع المنتجات</label>
                                                    </div>
                                                    @error('applies_to_all_products')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror

                                                    <div id="product-selection" class="mt-3" @if(old('applies_to_all_products', 0)) style="display: none;" @else style="display: block;" @endif>
                                                        <label class="form-label">اختر المنتجات التي ينطبق عليها الكوبون</label>
                                                        <div class="border p-3 rounded-3 mt-2 bg-light max-h-60 overflow-y-auto">
                                                            @foreach($products as $product)
                                                                <div class="form-check py-1">
                                                                    <input class="form-check-input" type="checkbox" id="product_{{ $product->id }}" name="product_ids[]" value="{{ $product->id }}" {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="product_{{ $product->id }}">{{ $product->name }}</label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @error('product_ids')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12 mb-3" id="packages-container">
                                                    <div id="package-selection" class="mt-3">
                                                        <label class="form-label">اختر الباقات التي ينطبق عليها الكوبون</label>
                                                        <div class="border p-3 rounded-3 mt-2 bg-light max-h-60 overflow-y-auto">
                                                            @foreach($packages as $package)
                                                                <div class="form-check py-1">
                                                                    <input class="form-check-input" type="checkbox" id="package_{{ $package->id }}" name="package_ids[]" value="{{ $package->id }}" {{ in_array($package->id, old('package_ids', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="package_{{ $package->id }}">
                                                                        {{ $package->name }}
                                                                        @if($package->services->count() > 0)
                                                                            <span class="badge bg-info-subtle text-info ms-1">
                                                                                {{ $package->services->pluck('name')->join(' - ') }}
                                                                            </span>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @error('package_ids')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_active">الكوبون نشط</label>
                                                    </div>
                                                    @error('is_active')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                حفظ الكوبون
                                            </button>
                                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-light-secondary me-2">
                                                إلغاء
                                            </a>
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
<link rel="stylesheet" href="{{ asset('assets/css/admin/coupon.css') }}?t={{ time() }}">
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('type');
        const valueHelp = document.getElementById('value-help');
        const allProductsCheckbox = document.getElementById('applies_to_all_products');
        const productSelection = document.getElementById('product-selection');
        const appliesToProductsCheckbox = document.getElementById('applies_to_products');
        const appliesToPackagesCheckbox = document.getElementById('applies_to_packages');
        const productsContainer = document.getElementById('products-container');
        const packagesContainer = document.getElementById('packages-container');
        const couponTypeCards = document.querySelectorAll('.coupon-type-card');
        const generateCouponBtn = document.getElementById('generate_coupon');
        const couponCodeInput = document.getElementById('coupon_code');

        function generateCouponCode(length = 8) {
            const characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            let result = '';
            const charactersLength = characters.length;

            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }

            return result;
        }

        if (generateCouponBtn) {
            generateCouponBtn.addEventListener('click', function() {
                const couponCode = generateCouponCode();
                couponCodeInput.value = couponCode;

                couponCodeInput.classList.add('bg-light-success');
                setTimeout(() => {
                    couponCodeInput.classList.remove('bg-light-success');
                }, 500);
            });
        }

        function updateValueHelp() {
            if (typeSelect.value === 'percentage') {
                valueHelp.textContent = 'أدخل النسبة المئوية بدون علامة % (مثال: 10 للحصول على خصم 10%)';
            } else {
                valueHelp.textContent = 'أدخل المبلغ بالريال السعودي (مثال: 50 للحصول على خصم 50 ر.س)';
            }
        }

        function toggleProductSelection() {
            productSelection.style.display = allProductsCheckbox.checked ? 'none' : 'block';
        }

        function toggleContainersVisibility() {
            productsContainer.style.display = appliesToProductsCheckbox.checked ? 'block' : 'none';
            packagesContainer.style.display = appliesToPackagesCheckbox.checked ? 'block' : 'none';
        }

        couponTypeCards.forEach(card => {
            card.addEventListener('click', function() {
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;

                const event = new Event('change');
                checkbox.dispatchEvent(event);
            });
        });

        typeSelect.addEventListener('change', updateValueHelp);
        allProductsCheckbox.addEventListener('change', toggleProductSelection);
        appliesToProductsCheckbox.addEventListener('change', toggleContainersVisibility);
        appliesToPackagesCheckbox.addEventListener('change', toggleContainersVisibility);

        updateValueHelp();
        toggleProductSelection();
        toggleContainersVisibility();
    });
</script>
@endsection
