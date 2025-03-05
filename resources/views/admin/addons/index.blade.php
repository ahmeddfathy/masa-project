@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">إدارة الإضافات</h3>
                    <a href="{{ route('admin.addons.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        إضافة إضافة جديدة
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-wrapper">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>الوصف</th>
                                        <th>السعر</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($addons as $addon)
                                        <tr>
                                            <td>{{ $addon->id }}</td>
                                            <td>{{ $addon->name }}</td>
                                            <td>{{ Str::limit($addon->description, 50) }}</td>
                                            <td>{{ $addon->price }} درهم</td>
                                            <td>
                                                <span class="badge {{ $addon->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $addon->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.addons.edit', $addon) }}"
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit me-1"></i>
                                                        تعديل
                                                    </a>
                                                    <form action="{{ route('admin.addons.destroy', $addon) }}"
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
                                                    <i class="fas fa-puzzle-piece empty-state-icon"></i>
                                                    <h4>لا توجد إضافات</h4>
                                                    <p class="text-muted">لم يتم إضافة أي إضافات بعد</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if($addons->hasPages())
                <div class="card-footer border-0 py-3">
                    <nav aria-label="صفحات الإضافات">
                        <ul class="pagination mb-0">
                            {{-- Previous Page Link --}}
                            @if ($addons->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $addons->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($addons->getUrlRange(1, $addons->lastPage()) as $page => $url)
                                @if ($page == $addons->currentPage())
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
                            @if ($addons->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $addons->nextPageUrl() }}" rel="next">
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
    <link rel="stylesheet" href="/assets/css/admin/addons.css">
@endsection
