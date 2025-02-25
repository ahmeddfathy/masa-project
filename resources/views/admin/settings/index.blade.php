@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">إعدادات الحجز</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">تعديل إعدادات الحجز</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="max_concurrent_bookings" class="font-weight-bold">
                        الحد الأقصى للحجوزات المتزامنة
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number"
                               class="form-control @error('max_concurrent_bookings') is-invalid @enderror"
                               id="max_concurrent_bookings"
                               name="max_concurrent_bookings"
                               value="{{ old('max_concurrent_bookings', $settings['max_concurrent_bookings']) }}"
                               min="1">
                        <div class="input-group-append">
                            <span class="input-group-text">حجز</span>
                        </div>
                    </div>
                    @error('max_concurrent_bookings')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">هذا الرقم يحدد عدد الحجوزات المسموح بها في نفس الوقت</small>
                </div>

                <div class="form-group mb-3">
                    <div class="custom-control custom-switch">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="show_store_appointments"
                               name="show_store_appointments"
                               value="1"
                               {{ $settings['show_store_appointments'] ? 'checked' : '' }}>
                        <label class="custom-control-label font-weight-bold" for="show_store_appointments">
                            إظهار خيارات حجز مواعيد المتجر للمستخدمين
                        </label>
                    </div>
                    <small class="form-text text-muted">هذا الإعداد يتحكم في ظهور قائمة حجز مواعيد المتجر في واجهة المستخدم</small>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save ml-1"></i>
                        حفظ الإعدادات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .invalid-feedback {
        display: block;
    }
</style>
@endpush
