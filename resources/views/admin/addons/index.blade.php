@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">إدارة الإضافات</h3>
                    <a href="{{ route('admin.addons.create') }}" class="btn btn-primary">
                        إضافة خدمة إضافية
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الباقة</th>
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
                                        <td>{{ $addon->package->name }}</td>
                                        <td>{{ Str::limit($addon->description, 50) }}</td>
                                        <td>{{ $addon->price }} درهم</td>
                                        <td>
                                            <span class="badge {{ $addon->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $addon->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.addons.edit', $addon) }}"
                                               class="btn btn-sm btn-info">
                                                تعديل
                                            </a>
                                            <form action="{{ route('admin.addons.destroy', $addon) }}"
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
                                        <td colspan="6" class="text-center">لا توجد إضافات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $addons->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/addons.css') }}">
@endsection
