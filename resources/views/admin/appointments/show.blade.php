@extends('layouts.admin')

@section('title', 'تفاصيل الموعد')
@section('page_title', 'تفاصيل الموعد')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title mb-0">تفاصيل الموعد</h2>
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-right"></i>
            العودة للمواعيد
        </a>
    </div>

    <!-- Reference Number Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="reference-label text-muted">
                        <i class="fas fa-hashtag me-2"></i>
                        رقم المرجع:
                    </div>
                    <div class="reference-number fs-4 fw-bold text-primary">
                        {{ $appointment->reference_number }}
                    </div>
                </div>
                <button class="btn btn-outline-primary"
                        onclick="navigator.clipboard.writeText('{{ $appointment->reference_number }}')"
                        title="نسخ الرقم المرجعي">
                    <i class="fas fa-copy me-2"></i>
                    نسخ الرقم المرجعي
                </button>
            </div>
        </div>
    </div>

    <!-- Rest of the content -->
    <div class="appointment-container">
        <!-- Appointment Details -->
        <div class="row g-4">
            <!-- Customer Information -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            معلومات العميل
                        </h5>
                        <div class="customer-details">
                            <!-- Basic Info -->
                            <div class="detail-item mb-4">
                                <dt><i class="fas fa-user"></i> الاسم</dt>
                                <dd>{{ $appointment->user->name }}</dd>
                            </div>
                            <div class="detail-item mb-4">
                                <dt><i class="fas fa-envelope"></i> البريد الإلكتروني</dt>
                                <dd>{{ $appointment->user->email }}</dd>
                            </div>

                            <!-- Phone Numbers -->
                            <div class="detail-section mb-4">
                                <h6 class="section-title mb-3">
                                    <i class="fas fa-phone-alt text-primary me-2"></i>
                                    أرقام الهواتف
                                </h6>
                                @forelse($appointment->user->phoneNumbers as $phone)
                                    <div class="phone-item mb-2 p-2 rounded {{ $phone->is_primary ? 'bg-light border' : '' }}">
                                        <div class="d-flex align-items-center">
                                            <span class="phone-number">
                                                {{ $phone->phone }}
                                            </span>
                                            @if($phone->is_primary)
                                                <span class="badge bg-primary ms-2">رئيسي</span>
                                            @endif
                                            <span class="phone-type text-muted ms-2">
                                                ({{ $phone->type_text }})
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">لا توجد أرقام هواتف مسجلة</p>
                                @endforelse
                            </div>

                            <!-- Addresses -->
                            <div class="detail-section">
                                <h6 class="section-title mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                    العناوين
                                </h6>
                                @forelse($appointment->user->addresses as $address)
                                    <div class="address-item mb-3 p-3 border rounded {{ $address->is_primary ? 'border-primary' : '' }}">
                                        <div class="d-flex justify-content-between">
                                            <div class="address-type">
                                                <span class="fw-bold">{{ $address->type_text }}</span>
                                                @if($address->is_primary)
                                                    <span class="badge bg-primary ms-2">رئيسي</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="address-details mt-2">
                                            <p class="mb-1"><i class="fas fa-city me-2"></i>{{ $address->city }}</p>
                                            <p class="mb-1"><i class="fas fa-map me-2"></i>{{ $address->area }}</p>
                                            <p class="mb-1"><i class="fas fa-road me-2"></i>{{ $address->street }}</p>
                                            @if($address->building_no)
                                                <p class="mb-1"><i class="fas fa-building me-2"></i>مبنى {{ $address->building_no }}</p>
                                            @endif
                                            @if($address->details)
                                                <p class="mb-0 text-muted"><i class="fas fa-info-circle me-2"></i>{{ $address->details }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">لا توجد عناوين مسجلة</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Information -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-calendar-check text-primary me-2"></i>
                            معلومات الموعد
                        </h5>
                        <div class="info-section">
                            <h5 class="section-title">
                                <i class="bi bi-info-circle me-2"></i>
                                معلومات الموعد
                            </h5>
                            <div class="row g-4">
                                <!-- Service Type -->
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        <div>
                                            <div class="info-label">نوع الخدمة</div>
                                            <div class="info-value">
                                                @if($appointment->service_type === 'custom_design')
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-paint-brush me-1"></i>
                                                        تصميم مخصص
                                                    </span>
                                                @else
                                                    {{ $appointment->service_type }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <dt><i class="fas fa-calendar"></i> التاريخ والوقت</dt>
                                    <dd>
                                        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M, Y') }}
                                        <br>
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                    </dd>
                                </div>
                                <div class="detail-item">
                                    <dt><i class="fas fa-clock"></i> المدة</dt>
                                    <dd>{{ $appointment->duration ?? 'غير محدد' }}</dd>
                                </div>
                                <div class="detail-item">
                                    <dt><i class="fas fa-tag"></i> السعر</dt>
                                    <dd>{{ $appointment->price ? number_format($appointment->price, 2) . ' ريال' : 'غير محدد' }}</dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Information -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            حالة الموعد
                        </h5>
                        <div class="status-details">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="status-badge {{ $appointment->status }}">
                                        <i class="fas fa-circle status-icon"></i>
                                        {{ $appointment->status_text }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <form action="{{ route('admin.appointments.update-status', $appointment) }}"
                                          method="POST"
                                          class="d-flex gap-2 justify-content-md-end align-items-center">
                                        @csrf
                                        @method('PATCH')

                                        <select name="status" class="form-select" style="max-width: 200px;">
                                            <option value="pending" {{ $appointment->status === 'pending' ? 'selected' : '' }}>
                                                قيد الانتظار
                                            </option>
                                            <option value="approved" {{ $appointment->status === 'approved' ? 'selected' : '' }}>
                                                موافقة
                                            </option>
                                            <option value="completed" {{ $appointment->status === 'completed' ? 'selected' : '' }}>
                                                مكتمل
                                            </option>
                                            <option value="rejected" {{ $appointment->status === 'rejected' ? 'selected' : '' }}>
                                                مرفوض
                                            </option>
                                            <option value="cancelled" {{ $appointment->status === 'cancelled' ? 'selected' : '' }}>
                                                ملغي
                                            </option>
                                        </select>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            تحديث الحالة
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Edit DateTime Form -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="mb-3">
                    <i class="fas fa-edit text-primary me-2"></i>
                    تعديل الموعد
                </h6>
                <form action="{{ route('admin.appointments.update-datetime', $appointment) }}" method="POST" class="row g-3">
                    @csrf
                    @method('PATCH')

                    <div class="col-md-6">
                        <label for="appointment_date" class="form-label">التاريخ</label>
                        <input type="date"
                               class="form-control @error('appointment_date') is-invalid @enderror"
                               id="appointment_date"
                               name="appointment_date"
                               value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}">
                        @error('appointment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="appointment_time" class="form-label">الوقت</label>
                        <input type="time"
                               class="form-control @error('appointment_time') is-invalid @enderror"
                               id="appointment_time"
                               name="appointment_time"
                               value="{{ old('appointment_time', $appointment->appointment_time->format('H:i')) }}">
                        @error('appointment_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                  id="notes"
                                  name="notes"
                                  rows="3">{{ old('notes', $appointment->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            <!-- Related Orders -->
            @if($appointment->orderItems->isNotEmpty())
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fas fa-shopping-cart text-primary me-2"></i>
                            الطلبات المرتبطة
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>المنتج</th>
                                        <th>السعر</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الطلب</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($appointment->orderItems as $item)
                                    <tr>
                                        <td>#{{ $item->order->id }}</td>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }} ريال</td>
                                        <td>
                                            <span class="badge bg-{{ $item->order->order_status === 'completed' ? 'success' : 'warning' }}">
                                                {{ $item->order->order_status }}
                                            </span>
                                        </td>
                                        <td>{{ $item->order->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $item->order) }}"
                                               class="btn btn-sm btn-light-primary">
                                                <i class="fas fa-eye"></i>
                                                عرض الطلب
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Notes Section -->
    @if($appointment->notes)
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="fas fa-sticky-note text-primary me-2"></i>
                    ملاحظات
                </h5>
                <div class="notes-text">
                    {{ $appointment->notes }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/admin/appointments.css') }}?t={{ time() }}">
@endsection
