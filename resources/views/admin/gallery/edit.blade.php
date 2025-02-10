@extends('layouts.admin')

@section('title', 'تعديل الصورة')

@section('content')
<div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">تعديل الصورة</h3>
                </div>
                <div class="card-body">
            <form action="{{ route('admin.gallery.update', $gallery) }}"
                  method="POST"
                  enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="caption" class="form-label">وصف الصورة</label>
                            <input type="text"
                                   class="form-control @error('caption') is-invalid @enderror"
                                   id="caption"
                                   name="caption"
                                   value="{{ old('caption', $gallery->caption) }}"
                                   required>
                            @error('caption')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">التصنيف</label>
                            <input type="text"
                                   class="form-control @error('category') is-invalid @enderror"
                                   id="category"
                                   name="category"
                                   value="{{ old('category', $gallery->category) }}"
                                   required>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">تغيير الصورة (اختياري)</label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                اترك هذا الحقل فارغاً إذا كنت لا تريد تغيير الصورة.
                                يجب أن تكون الصورة الجديدة من نوع: jpeg, png, jpg
                            </small>
                        </div>
                            </div>

                    <div class="col-md-4">
                        <div class="current-image mb-3">
                            <label class="form-label">الصورة الحالية</label>
                            <img src="{{ Storage::url($gallery->image_url) }}"
                                 alt="{{ $gallery->caption }}"
                                 class="img-fluid rounded">
                        </div>
                        <div class="image-preview mb-3">
                            <label class="form-label">معاينة الصورة الجديدة</label>
                            <img id="preview" src="#"
                                 alt="معاينة الصورة"
                                 class="img-fluid rounded"
                                 style="display: none;">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // معاينة الصورة قبل الرفع
    document.getElementById('image').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            const preview = document.getElementById('preview');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
    }
    };
</script>
@endpush
@endsection
