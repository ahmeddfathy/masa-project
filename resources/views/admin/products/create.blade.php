@extends('layouts.admin')

@section('title', 'إضافة منتج جديد')
@section('page_title', 'إضافة منتج جديد')

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
                                            <i class="fas fa-plus text-primary me-2"></i>
                                            إضافة منتج جديد
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.products.index') }}" class="btn btn-light-secondary">
                                                <i class="fas fa-arrow-right me-1"></i>
                                                عودة للمنتجات
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add this after the form opening tag -->
                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Form -->
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-4">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                معلومات أساسية
                                            </h5>
                                            <div class="mb-3">
                                                <label class="form-label">اسم المنتج</label>
                                                <input type="text" name="name" class="form-control shadow-sm @error('name') is-invalid @enderror" value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">التصنيف</label>
                                                <select name="category_id" class="form-select shadow-sm @error('category_id') is-invalid @enderror">
                                                    <option value="">اختر التصنيف</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">السعر (ريال)</label>
                                                <input type="number" name="price" class="form-control shadow-sm @error('price') is-invalid @enderror" value="{{ old('price') }}">
                                                @error('price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">المخزون</label>
                                                <input type="number" name="stock" class="form-control shadow-sm @error('stock') is-invalid @enderror" value="{{ old('stock') }}">
                                                @error('stock')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="isAvailable"
                                                           name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="isAvailable">متاح للبيع</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description and Images -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-image text-primary me-2"></i>
                                                الوصف والصور
                                            </h5>
                                            <div class="mb-3">
                                                <label class="form-label">الوصف</label>
                                                <textarea name="description" class="form-control shadow-sm @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">صور المنتج</label>
                                                @error('images.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('is_primary.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="imagesContainer">
                                                    <div class="mb-2">
                                                        <div class="input-group shadow-sm">
                                                            <input type="file" name="images[]" class="form-control" accept="image/*">
                                                            <div class="input-group-text">
                                                                <label class="mb-0">
                                                                    <input type="radio" name="is_primary[0]" value="1" class="me-1">
                                                                    صورة رئيسية
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-light-secondary btn-sm mt-2" onclick="addImageInput()">
                                                    <i class="fas fa-plus"></i>
                                                    إضافة صورة
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Colors -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-palette text-primary me-2"></i>
                                                    الألوان المتاحة
                                                </h5>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           id="hasColors"
                                                           name="has_colors"
                                                           value="1"
                                                           {{ old('has_colors') || old('colors') ? 'checked' : '' }}
                                                           onchange="toggleColorsSection(this)">
                                                    <label class="form-check-label" for="hasColors">تفعيل الألوان</label>
                                                </div>
                                            </div>
                                            <div id="colorsSection" style="display: {{ old('has_colors') ? 'block' : 'none' }}">
                                                @error('colors.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('color_available.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="colorsContainer">
                                                    @if(old('colors'))
                                                        @foreach(old('colors') as $index => $color)
                                                            <div class="input-group mb-2 shadow-sm">
                                                                <input type="text"
                                                                       name="colors[]"
                                                                       class="form-control @error('colors.'.$index) is-invalid @enderror"
                                                                       placeholder="اسم اللون"
                                                                       value="{{ $color }}">
                                                                <div class="input-group-text">
                                                                    <label class="mb-0">
                                                                        <input type="checkbox"
                                                                               name="color_available[]"
                                                                               value="1"
                                                                               {{ !isset(old('color_available')[$index]) || old('color_available')[$index] ? 'checked' : '' }}
                                                                               class="me-1">
                                                                        متوفر
                                                                    </label>
                                                                </div>
                                                                <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="input-group mb-2 shadow-sm">
                                                            <input type="text" name="colors[]" class="form-control" placeholder="اسم اللون">
                                                            <div class="input-group-text">
                                                                <label class="mb-0">
                                                                    <input type="checkbox" name="color_available[]" value="1" checked class="me-1">
                                                                    متوفر
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-light-secondary btn-sm" onclick="addColorInput()">
                                                    <i class="fas fa-plus"></i>
                                                    إضافة لون
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sizes -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-4">
                                                <h5 class="card-title mb-0">
                                                    <i class="fas fa-ruler text-primary me-2"></i>
                                                    المقاسات المتاحة
                                                </h5>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           id="hasSizes"
                                                           name="has_sizes"
                                                           value="1"
                                                           {{ old('has_sizes') || old('sizes') ? 'checked' : '' }}
                                                           onchange="toggleSizesSection(this)">
                                                    <label class="form-check-label" for="hasSizes">تفعيل المقاسات</label>
                                                </div>
                                            </div>
                                            <div id="sizesSection" style="display: {{ old('has_sizes') ? 'block' : 'none' }}">
                                                @error('sizes.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('size_available.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="sizesContainer">
                                                    @if(old('sizes'))
                                                        @foreach(old('sizes') as $index => $size)
                                                            <div class="input-group mb-2 shadow-sm">
                                                                <input type="text"
                                                                       name="sizes[]"
                                                                       class="form-control @error('sizes.'.$index) is-invalid @enderror"
                                                                       placeholder="المقاس"
                                                                       value="{{ $size }}">
                                                                <div class="input-group-text">
                                                                    <label class="mb-0">
                                                                        <input type="checkbox"
                                                                               name="size_available[]"
                                                                               value="1"
                                                                               {{ !isset(old('size_available')[$index]) || old('size_available')[$index] ? 'checked' : '' }}
                                                                               class="me-1">
                                                                        متوفر
                                                                    </label>
                                                                </div>
                                                                <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <div class="input-group mb-2 shadow-sm">
                                                            <input type="text" name="sizes[]" class="form-control" placeholder="المقاس">
                                                            <div class="input-group-text">
                                                                <label class="mb-0">
                                                                    <input type="checkbox" name="size_available[]" value="1" checked class="me-1">
                                                                    متوفر
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-light-secondary btn-sm" onclick="addSizeInput()">
                                                    <i class="fas fa-plus"></i>
                                                    إضافة مقاس
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Product Options -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-cog text-primary me-2"></i>
                                                خيارات المنتج
                                            </h5>
                                            <div class="row g-3">
                                                <!-- Appointment Option -->
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="enable_appointments" name="enable_appointments"
                                                               value="1" checked>
                                                        <label class="form-check-label" for="enable_appointments">
                                                            <i class="fas fa-calendar-check me-2"></i>
                                                            السماح بحجز موعد للمقاسات
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Color Selection Option -->
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="enable_color_selection" name="enable_color_selection"
                                                               value="1">
                                                        <label class="form-check-label" for="enable_color_selection">
                                                            <i class="fas fa-palette me-2"></i>
                                                            السماح باختيار الألوان المحددة
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Custom Color Option -->
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="enable_custom_color" name="enable_custom_color"
                                                               value="1">
                                                        <label class="form-check-label" for="enable_custom_color">
                                                            <i class="fas fa-paint-brush me-2"></i>
                                                            السماح بإضافة لون مخصص
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Size Selection Option -->
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="enable_size_selection" name="enable_size_selection"
                                                               value="1">
                                                        <label class="form-check-label" for="enable_size_selection">
                                                            <i class="fas fa-ruler me-2"></i>
                                                            السماح باختيار المقاسات المحددة
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Custom Size Option -->
                                                <div class="col-md-6">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="enable_custom_size" name="enable_custom_size"
                                                               value="1">
                                                        <label class="form-check-label" for="enable_custom_size">
                                                            <i class="fas fa-ruler-combined me-2"></i>
                                                            السماح بإضافة مقاس مخصص
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>ملاحظة:</strong> هذه الإعدادات تتحكم في الخيارات المتاحة للعملاء عند طلب هذا المنتج.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                حفظ المنتج
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
<link rel="stylesheet" href="{{ asset('assets/css/admin/products.css') }}">
@endsection

@section('scripts')
<script>
let imageCount = 1;

function addImageInput() {
    const container = document.getElementById('imagesContainer');
    const div = document.createElement('div');
    div.className = 'mb-2';
    div.innerHTML = `
        <div class="input-group shadow-sm">
            <input type="file" name="images[]" class="form-control" accept="image/*">
            <div class="input-group-text">
                <label class="mb-0">
                    <input type="radio" name="is_primary[${imageCount}]" value="1" class="me-1">
                    صورة رئيسية
                </label>
            </div>
            <button type="button" class="btn btn-light-danger" onclick="this.closest('.mb-2').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    imageCount++;
}

function addColorInput() {
    const container = document.getElementById('colorsContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 shadow-sm';
    div.innerHTML = `
        <input type="text" name="colors[]" class="form-control" placeholder="اسم اللون">
        <div class="input-group-text">
            <label class="mb-0">
                <input type="checkbox" name="color_available[]" value="1" checked class="me-1">
                متوفر
            </label>
        </div>
        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}

function addSizeInput() {
    const container = document.getElementById('sizesContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 shadow-sm';
    div.innerHTML = `
        <input type="text" name="sizes[]" class="form-control" placeholder="المقاس">
        <div class="input-group-text">
            <label class="mb-0">
                <input type="checkbox" name="size_available[]" value="1" checked class="me-1">
                متوفر
            </label>
        </div>
        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(div);
}

function toggleColorsSection(checkbox) {
    const section = document.getElementById('colorsSection');
    section.style.display = checkbox.checked ? 'block' : 'none';
    if (!checkbox.checked) {
        if (document.querySelectorAll('#colorsContainer input[name="colors[]"]').length > 0) {
            if (!confirm('هل أنت متأكد من إلغاء تفعيل الألوان؟ سيتم حذف جميع الألوان المدخلة.')) {
                checkbox.checked = true;
                return;
            }
        }
        document.getElementById('colorsContainer').innerHTML = '';
    } else if (document.querySelectorAll('#colorsContainer input[name="colors[]"]').length === 0) {
        addColorInput();
    }
}

function toggleSizesSection(checkbox) {
    const section = document.getElementById('sizesSection');
    section.style.display = checkbox.checked ? 'block' : 'none';
    if (!checkbox.checked) {
        if (document.querySelectorAll('#sizesContainer input[name="sizes[]"]').length > 0) {
            if (!confirm('هل أنت متأكد من إلغاء تفعيل المقاسات؟ سيتم حذف جميع المقاسات المدخلة.')) {
                checkbox.checked = true;
                return;
            }
        }
        document.getElementById('sizesContainer').innerHTML = '';
    } else if (document.querySelectorAll('#sizesContainer input[name="sizes[]"]').length === 0) {
        addSizeInput();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const hasColors = {{ old('has_colors') || !empty(old('colors')) ? 'true' : 'false' }};
    const hasSizes = {{ old('has_sizes') || !empty(old('sizes')) ? 'true' : 'false' }};

    const colorsCheckbox = document.getElementById('hasColors');
    const sizesCheckbox = document.getElementById('hasSizes');

    colorsCheckbox.checked = hasColors;
    sizesCheckbox.checked = hasSizes;

    document.getElementById('colorsSection').style.display = hasColors ? 'block' : 'none';
    document.getElementById('sizesSection').style.display = hasSizes ? 'block' : 'none';
});
</script>
@endsection
