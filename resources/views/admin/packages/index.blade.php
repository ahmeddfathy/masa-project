@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">إدارة الباقات</h3>
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        إضافة باقة جديدة
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
                                        <th>السعر الأساسي</th>
                                        <th>المدة</th>
                                        <th>عدد الصور</th>
                                        <th>عدد الثيمات</th>
                                        <th>الخدمات</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($packages as $package)
                                        <tr>
                                            <td>{{ $package->id }}</td>
                                            <td>{{ $package->name }}</td>
                                            <td>{{ $package->base_price }} درهم</td>
                                            <td>
                                                @if($package->duration >= 60)
                                                    {{ floor($package->duration / 60) }} ساعة
                                                    @if($package->duration % 60 > 0)
                                                        و {{ $package->duration % 60 }} دقيقة
                                                    @endif
                                                @else
                                                    {{ $package->duration }} دقيقة
                                                @endif
                                            </td>
                                            <td>{{ $package->num_photos }}</td>
                                            <td>{{ $package->themes_count }}</td>
                                            <td>
                                                @foreach($package->services as $service)
                                                    <span class="badge bg-info">{{ $service->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge {{ $package->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $package->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.packages.edit', $package) }}"
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit me-1"></i>
                                                        تعديل
                                                    </a>
                                                    <form action="{{ route('admin.packages.destroy', $package) }}"
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
                                            <td colspan="9" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-box-open empty-state-icon"></i>
                                                    <h4>لا توجد باقات</h4>
                                                    <p class="text-muted">لم يتم إضافة أي باقات بعد</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @if($packages->hasPages())
                <div class="card-footer border-0 py-3">
                    <nav aria-label="صفحات الباقات">
                        <ul class="pagination mb-0">
                            {{-- Previous Page Link --}}
                            @if ($packages->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $packages->previousPageUrl() }}" rel="prev">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($packages->getUrlRange(1, $packages->lastPage()) as $page => $url)
                                @if ($page == $packages->currentPage())
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
                            @if ($packages->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $packages->nextPageUrl() }}" rel="next">
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
    <link rel="stylesheet" href="{{ asset('assets/css/admin/packages.css') }}?t={{ time() }}">
@endsection
