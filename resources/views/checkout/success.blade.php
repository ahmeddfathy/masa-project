@extends('layouts.customer')

@section('title', 'تم تأكيد الطلب')

@section('content')
<div class="container py-5">
    <div class="success-message text-center">
        <div class="success-icon mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
        </div>
        <h2 class="mb-4">تم تأكيد طلبك بنجاح</h2>
        <!-- تم تحديث عرض رقم الطلب -->
        <p class="lead">رقم الطلب: #{{ $order->order_number }}</p>
        <p>سيتم إرسال تفاصيل الطلب إلى بريدك الإلكتروني</p>

        <div class="mt-4">
            <!-- تم تحديث الرابط ليستخدم UUID -->
            <a href="{{ route('orders.show', $order->uuid) }}" class="btn btn-primary me-2">
                عرض تفاصيل الطلب
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                عرض جميع الطلبات
            </a>
        </div>
    </div>
</div>
@endsection
