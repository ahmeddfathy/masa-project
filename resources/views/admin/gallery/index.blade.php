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
            <i class="fas fa-plus me-2"></i>
            إضافة صورة جديدة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="gallery-container">
        <div class="gallery-scroll">
            <div class="gallery-grid">
                @forelse($images as $image)
                    <div class="gallery-item">
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
                                            <i class="fas fa-edit me-1"></i>
                                            تعديل
                                        </a>
                                        <form action="{{ route('admin.gallery.destroy', $image) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الصورة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash me-1"></i>
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="w-100">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <h3 class="empty-state-title">لا توجد صور</h3>
                            <p class="empty-state-text">قم بإضافة صور جديدة للمعرض</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($images->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="صفحات المعرض">
            <ul class="pagination mb-0">
                {{-- Previous Page Link --}}
                @if ($images->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $images->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($images->getUrlRange(1, $images->lastPage()) as $page => $url)
                    @if ($page == $images->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($images->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $images->nextPageUrl() }}" rel="next">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link" aria-hidden="true">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
</div>
@endsection
