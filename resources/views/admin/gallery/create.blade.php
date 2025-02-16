@extends('layouts.admin')

@section('title', 'إضافة صورة جديدة')

@section('content')
<div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إضافة صورة جديدة</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="caption" class="form-label">وصف الصورة</label>
                            <input type="text"
                                   class="form-control @error('caption') is-invalid @enderror"
                                   id="caption"
                                   name="caption"
                                   value="{{ old('caption') }}"
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
                                   value="{{ old('category') }}"
                                   required>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">الصورة</label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*"
                                   required>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                يجب أن تكون الصورة من نوع: jpeg, png, jpg
                            </small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="image-preview mb-3">
                            <img id="preview" src="#" alt="معاينة الصورة" style="max-width: 100%; display: none;">
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">حفظ</button>
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

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/gallery.css') }}">
@endsection
@endsection
