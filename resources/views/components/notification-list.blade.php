<div class="space-y-4">
  @forelse($notifications as $notification)
  <div class="bg-white rounded-lg shadow p-4 {{ $notification->read_at ? 'opacity-75' : '' }}">
    <div class="flex justify-between items-start">
      <div>
        <h4 class="text-sm font-medium text-gray-900">
          {{ $notification->data['title'] ?? 'Notification' }}
        </h4>
        <p class="mt-1 text-sm text-gray-500">
          {{ $notification->data['message'] ?? '' }}
        </p>
        <p class="mt-2 text-xs text-gray-400">
          {{ $notification->created_at->diffForHumans() }}
        </p>
      </div>
      @unless($notification->read_at)
      <form action="{{ route('notifications.mark-read', $notification) }}" method="POST">
        @csrf
        <button type="submit"
          class="text-sm text-blue-600 hover:text-blue-800">
          Mark as Read
        </button>
      </form>
      @endunless
    </div>
  </div>
  @empty
  <div class="text-center py-4">
    <p class="text-sm text-gray-500">No notifications</p>
  </div>
  @endforelse

  @unless($showAll)
  @if($notifications->count() > 0)
  <div class="text-center">
    <a href="{{ route('notifications.index') }}"
      class="text-sm text-blue-600 hover:text-blue-800">
      View All Notifications
    </a>
  </div>
  @endif
  @endunless
</div>
