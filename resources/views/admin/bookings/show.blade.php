@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تفاصيل الحجز #{{ $booking->booking_number }}</h3>
                    <div>
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">عودة للحجوزات</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- معلومات العميل -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">معلومات العميل</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>الاسم:</strong> {{ $booking->user->name }}</p>
                                    <p><strong>البريد الإلكتروني:</strong> {{ $booking->user->email }}</p>
                                    <p><strong>رقم الهاتف:</strong> {{ $booking->user->phone }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات الحجز -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">معلومات الحجز</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>نوع الجلسة:</strong> {{ $booking->service->name }}</p>
                                    <p><strong>الباقة:</strong> {{ $booking->package->name }}</p>
                                    <p><strong>تاريخ الجلسة:</strong> {{ $booking->session_date->format('Y-m-d') }}</p>
                                    <p><strong>وقت الجلسة:</strong> {{ $booking->session_time->format('H:i') }}</p>
                                    <p><strong>المبلغ الإجمالي:</strong> {{ $booking->total_amount }} درهم</p>
                                </div>
                            </div>
                        </div>

                        <!-- معلومات المولود -->
                        @if($booking->baby_name || $booking->baby_birth_date || $booking->gender)
                        <div class="col-md-6 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">معلومات المولود</h5>
                                </div>
                                <div class="card-body">
                                    @if($booking->baby_name)
                                        <p><strong>الاسم:</strong> {{ $booking->baby_name }}</p>
                                    @endif
                                    @if($booking->baby_birth_date)
                                        <p><strong>تاريخ الميلاد:</strong> {{ $booking->baby_birth_date->format('Y-m-d') }}</p>
                                    @endif
                                    @if($booking->gender)
                                        <p><strong>الجنس:</strong> {{ $booking->gender }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- الإضافات -->
                        @if($booking->addons->count() > 0)
                        <div class="col-md-6 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">الإضافات المختارة</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        @foreach($booking->addons as $addon)
                                            <li>
                                                {{ $addon->name }} -
                                                {{ $addon->pivot->quantity }} × {{ $addon->pivot->price_at_booking }} درهم
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- معلومات الدفع -->
                        <div class="col-md-6 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">معلومات الدفع</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>حالة الدفع:</strong> {{ $booking->payment_status }}</p>
                                    @if($booking->payment_transaction_id)
                                        <p><strong>رقم المعاملة:</strong> {{ $booking->payment_transaction_id }}</p>
                                    @endif
                                    @if($booking->payment_id)
                                        <p><strong>معرف الدفع:</strong> {{ $booking->payment_id }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- تحديث الحالة -->
                        <div class="col-12 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">تحديث حالة الحجز</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.bookings.update-status', $booking->uuid) }}"
                                          method="POST" class="d-flex align-items-center">
                                        @csrf
                                        @method('PATCH')
                                        <div class="row g-3 align-items-center w-100">
                                            <div class="col-md-8">
                                                <select name="status" class="form-select">
                                                    <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>
                                                        قيد الانتظار
                                                    </option>
                                                    <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>
                                                        مؤكد
                                                    </option>
                                                    <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>
                                                        مكتمل
                                                    </option>
                                                    <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>
                                                        ملغي
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-save me-1"></i>
                                                    تحديث الحالة
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- إضافة قسم لعرض رسائل النجاح -->
                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="/assets/css/admin/bookings.css">
@endsection
