@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">إدارة الباقات</h3>
                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                        إضافة باقة جديدة
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
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
                                        <td>{{ $package->duration }} ساعة</td>
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
                                            <a href="{{ route('admin.packages.edit', $package) }}"
                                               class="btn btn-sm btn-info">
                                                تعديل
                                            </a>
                                            <form action="{{ route('admin.packages.destroy', $package) }}"
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
                                        <td colspan="8" class="text-center">لا توجد باقات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $packages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
