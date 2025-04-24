@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">إدارة الخدمات</h3>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        إضافة خدمة جديدة
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-wrapper">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الصورة</th>
                                        <th>الاسم</th>
                                        <th>الوصف</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($services as $service)
                                        <tr>
                                            <td>{{ $service->id }}</td>
                                            <td>
                                                @if($service->image)
                                                    <img src="{{ url('storage/' . $service->image) }}" alt="{{ $service->name }}"
                                                         class="img-thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="text-center" style="width: 80px;">
                                                        <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $service->name }}</td>
                                            <td>{{ Str::limit($service->description, 50) }}</td>
                                            <td>
                                                <span class="badge {{ $service->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $service->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.services.edit', $service) }}"
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit me-1"></i>
                                                        تعديل
                                                    </a>
                                                    <form action="{{ route('admin.services.destroy', $service) }}"
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                            <i class="fas fa-trash me-1"></i>
                                                            حذف
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-cogs empty-state-icon"></i>
                                                    <h4>لا توجد خدمات</h4>
                                                    <p class="text-muted">لم يتم إضافة أي خدمات بعد</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if($services->hasPages())
                <div class="card-footer border-0 py-3">
                    <nav aria-label="صفحات الخدمات">
                        <ul class="pagination mb-0">
                            {{-- Previous Page Link --}}
                            @if ($services->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $services->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($services->getUrlRange(1, $services->lastPage()) as $page => $url)
                                @if ($page == $services->currentPage())
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
                            @if ($services->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $services->nextPageUrl() }}" rel="next">
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
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/services.css') }}?t={{ time() }}">
@endsection
