@extends('layouts.admin')

@section('title', 'تعديل المنتج - ' . $product->name)
@section('page_title', 'تعديل المنتج: ' . $product->name)

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
                                            <i class="fas fa-edit text-primary me-2"></i>
                                            تعديل المنتج
                                        </h5>
                                        <div class="actions">
                                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-light-info me-2">
                                                <i class="fas fa-eye me-1"></i>
                                                عرض المنتج
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

                        <!-- Add this after the form opening tag -->
                        @if($errors->any())
                            <div class="alert alert-danger mb-4">
                                <h5 class="alert-heading mb-2">يوجد أخطاء في النموذج:</h5>
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Debug information -->
                            @if(config('app.debug'))
                                <div class="alert alert-info mb-4">
                                    <h6>Debug Information:</h6>
                                    <pre>{{ print_r($errors->toArray(), true) }}</pre>
                                    <h6>Request Data:</h6>
                                    <pre>{{ print_r(request()->all(), true) }}</pre>
                                </div>
                            @endif
                        @endif

                        <!-- Form -->
                        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

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
                                                <input type="text" name="name"
                                                       class="form-control shadow-sm @error('name') is-invalid @enderror"
                                                       value="{{ old('name', $product->name) }}">
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">التصنيف</label>
                                                <select name="category_id"
                                                        class="form-select shadow-sm @error('category_id') is-invalid @enderror">
                                                    <option value="">اختر التصنيف</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                                @selected(old('category_id', $product->category_id) == $category->id)>
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
                                                <input type="number" name="price" class="form-control shadow-sm"
                                                       value="{{ old('price', $product->price) }}">
                                                @error('price')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">المخزون</label>
                                                <input type="number" name="stock" class="form-control shadow-sm"
                                                       value="{{ old('stock', $product->stock) }}">
                                                @error('stock')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="isAvailable"
                                                           name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="isAvailable">متاح للبيع</label>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">الرابط المختصر (Slug)</label>
                                                <input type="text" name="slug"
                                                       class="form-control shadow-sm @error('slug') is-invalid @enderror"
                                                       value="{{ old('slug', $product->slug) }}">
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">يجب أن يكون فريداً ولا يمكن تكراره مع منتج آخر</div>
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
                                                <textarea name="description" class="form-control shadow-sm" rows="4">{{ old('description', $product->description) }}</textarea>
                                                @error('description')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <!-- Current Images -->
                                            <div class="mb-3">
                                                <label class="form-label">الصور الحالية</label>
                                                <div class="row g-2 mb-2">
                                                    @foreach($product->images as $image)
                                                    <div class="col-auto">
                                                        <div class="position-relative">
                                                            <img src="{{ url('storage/' . $image->image_path) }}"
                                                                 alt="صورة المنتج"
                                                                 class="rounded"
                                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                                            <div class="position-absolute top-0 end-0 p-1">
                                                                <div class="form-check">
                                                                    <input type="radio" name="is_primary" value="{{ $image->id }}"
                                                                           class="form-check-input" @checked($image->is_primary)>
                                                                </div>
                                                            </div>
                                                            <div class="position-absolute bottom-0 start-0 p-1">
                                                                <div class="form-check">
                                                                    <input type="checkbox" name="remove_images[]" value="{{ $image->id }}"
                                                                           class="form-check-input">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <div class="small text-muted">
                                                    * حدد الصور للحذف
                                                    <br>
                                                    * اختر الصورة الرئيسية
                                                </div>
                                            </div>

                                            <!-- New Images -->
                                            <div class="mb-3">
                                                <label class="form-label">إضافة صور جديدة</label>
                                                @error('new_images.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('is_primary.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="newImagesContainer">
                                                    <div class="mb-2">
                                                        <div class="input-group shadow-sm">
                                                            <input type="file" name="new_images[]" class="form-control" accept="image/*">
                                                            <div class="input-group-text">
                                                                <label class="mb-0">
                                                                    <input type="radio" name="is_primary_new[0]" value="1" class="me-1">
                                                                    صورة رئيسية
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-light-secondary btn-sm mt-2" onclick="addNewImageInput()">
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
                                                           {{ $product->colors->count() > 0 || old('has_colors') ? 'checked' : '' }}
                                                           onchange="toggleColorsSection(this)">
                                                    <label class="form-check-label" for="hasColors">تفعيل الألوان</label>
                                                </div>
                                            </div>
                                            <div id="colorsSection" style="display: {{ $product->colors->count() > 0 ? 'block' : 'none' }}">
                                                @error('colors.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('color_ids.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('color_available.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="colorsContainer">
                                                    @foreach($product->colors as $color)
                                                    <div class="input-group mb-2 shadow-sm">
                                                        <input type="hidden" name="color_ids[]" value="{{ $color->id }}">
                                                        <input type="text" name="colors[]" class="form-control" placeholder="اسم اللون" value="{{ $color->color }}">
                                                        <div class="input-group-text">
                                                            <label class="mb-0">
                                                                <input type="checkbox" name="color_available[]" value="1" {{ $color->is_available ? 'checked' : '' }} class="me-1">
                                                                متوفر
                                                            </label>
                                                        </div>
                                                        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    @endforeach
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
                                                           {{ $product->sizes->count() > 0 || old('has_sizes') ? 'checked' : '' }}
                                                           onchange="toggleSizesSection(this)">
                                                    <label class="form-check-label" for="hasSizes">تفعيل المقاسات</label>
                                                </div>
                                            </div>
                                            <div id="sizesSection" style="display: {{ $product->sizes->count() > 0 ? 'block' : 'none' }}">
                                                @error('sizes.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('size_ids.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                @error('size_available.*')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <div id="sizesContainer">
                                                    @foreach($product->sizes as $size)
                                                    <div class="input-group mb-2 shadow-sm">
                                                        <input type="hidden" name="size_ids[]" value="{{ $size->id }}">
                                                        <input type="text" name="sizes[]" class="form-control" placeholder="المقاس" value="{{ $size->size }}">
                                                        <div class="input-group-text">
                                                            <label class="mb-0">
                                                                <input type="checkbox" name="size_available[]" value="1" {{ $size->is_available ? 'checked' : '' }} class="me-1">
                                                                متوفر
                                                            </label>
                                                        </div>
                                                        <button type="button" class="btn btn-light-danger" onclick="this.closest('.input-group').remove()">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                    @endforeach
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
                                                               value="1" {{ $product->enable_appointments ? 'checked' : '' }}>
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
                                                               value="1" {{ $product->enable_color_selection ? 'checked' : '' }}>
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
                                                               value="1" {{ $product->enable_custom_color ? 'checked' : '' }}>
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
                                                               value="1" {{ $product->enable_size_selection ? 'checked' : '' }}>
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
                                                               value="1" {{ $product->enable_custom_size ? 'checked' : '' }}>
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
<link rel="stylesheet" href="/assets/css/admin/products.css">
@endsection

@section('scripts')
<script>
let newImageCount = 1;

function toggleColorsSection(checkbox) {
    const colorsSection = document.getElementById('colorsSection');
    colorsSection.style.display = checkbox.checked ? 'block' : 'none';
    if (checkbox.checked && !document.querySelector('#colorsContainer .input-group')) {
        addColorInput();
    }
}

function toggleSizesSection(checkbox) {
    const sizesSection = document.getElementById('sizesSection');
    sizesSection.style.display = checkbox.checked ? 'block' : 'none';
    if (checkbox.checked && !document.querySelector('#sizesContainer .input-group')) {
        addSizeInput();
    }
}

function addNewImageInput() {
    const container = document.getElementById('newImagesContainer');
    const div = document.createElement('div');
    div.className = 'mb-2';
    div.innerHTML = `
        <div class="input-group shadow-sm">
            <input type="file" name="new_images[]" class="form-control" accept="image/*">
            <div class="input-group-text">
                <label class="mb-0">
                    <input type="radio" name="is_primary_new[${newImageCount}]" value="1" class="me-1">
                    صورة رئيسية
                </label>
            </div>
            <button type="button" class="btn btn-light-danger" onclick="this.closest('.mb-2').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    newImageCount++;
}

function addColorInput() {
    const container = document.getElementById('colorsContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 shadow-sm';
    div.innerHTML = `
        <input type="hidden" name="color_ids[]" value="">
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
        <input type="hidden" name="size_ids[]" value="">
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

</script>
@endsection
