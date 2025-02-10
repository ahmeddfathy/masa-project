@extends('layouts.customer')

@section('title', 'الإشعارات')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('assets/css/customer/notifications.css') }}">
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title mb-0">الإشعارات</h2>
        @if($notifications->count() > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-check2-all me-2"></i>
                تحديد الكل كمقروء
            </button>
        </form>
        @endif
    </div>

    <div class="notifications-container">
        @forelse($notifications as $notification)
        <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
            <div class="d-flex gap-3">
                <div class="notification-icon">
                    <i class="bi bi-bell"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h5 class="mb-1">{{ $notification->data['title'] ?? 'إشعار جديد' }}</h5>
                            <p class="mb-0">{{ $notification->data['message'] ?? '' }}</p>
                        </div>
                        @if(!$notification->read_at)
                        <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-check2"></i>
                                تحديد كمقروء
                            </button>
                        </form>
                        @endif
                    </div>
                    <div class="notification-time">
                        <i class="bi bi-clock me-1"></i>
                        {{ $notification->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-bell-slash"></i>
            </div>
            <h3>لا توجد إشعارات</h3>
            <p class="text-muted">ليس لديك أي إشعارات جديدة في الوقت الحالي</p>
        </div>
        @endforelse

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection
