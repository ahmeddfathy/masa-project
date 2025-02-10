@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">إدارة الخدمات</h3>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                        إضافة خدمة جديدة
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
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
                                        <td>{{ $service->name }}</td>
                                        <td>{{ Str::limit($service->description, 50) }}</td>
                                        <td>
                                            <span class="badge {{ $service->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $service->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.services.edit', $service) }}"
                                               class="btn btn-sm btn-info">
                                                تعديل
                                            </a>
                                            <form action="{{ route('admin.services.destroy', $service) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                    حذف
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">لا توجد خدمات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
