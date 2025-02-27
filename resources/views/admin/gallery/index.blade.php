@extends('layouts.admin')

@section('title', 'معرض الصور')

@section('styles')
    <link rel="stylesheet" href="/assets/css/admin/gallery.css">
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">معرض الصور</h2>
                    <a href="{{ route('admin.gallery.create') }}" class="btn btn-primary">
            <i class="fas fa-plus ml-1"></i>
                        إضافة صورة جديدة
                    </a>
                </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        @forelse($images as $image)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100">
                    <img src="{{ url('storage/' . $image->image_url) }}"
                         class="card-img-top"
                         alt="{{ $image->caption }}"
                         style="height: 200px; object-fit: cover;">
                <div class="card-body">
                        <h5 class="card-title">{{ $image->caption }}</h5>
                        <p class="card-text text-muted">{{ $image->category }}</p>
                                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.gallery.edit', $image) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                    تعديل
                                </a>
                                        <form action="{{ route('admin.gallery.destroy', $image) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذه الصورة؟');">
                                            @csrf
                                            @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                                حذف
                                            </button>
                                        </form>
                            </div>
                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h4>لا توجد صور</h4>
                    <p class="text-muted">قم بإضافة صور جديدة للمعرض</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $images->links() }}
    </div>
</div>
@endsection
