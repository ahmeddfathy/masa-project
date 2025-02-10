<div x-data="{ open: false }" class="ml-3 relative">
  <div>
    <button @click="open = !open" type="button"
      class="relative bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      id="notifications-menu-button">
      <span class="sr-only">Open notifications menu</span>
      <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
      </svg>
      @if($unreadCount > 0)
      <span class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full h-4 w-4 flex items-center justify-center text-xs">
        {{ $unreadCount }}
      </span>
      @endif
    </button>
  </div>

  <div x-show="open"
    @click.away="open = false"
    x-transition:enter="transition ease-out duration-100"
    x-transition:enter-start="transform opacity-0 scale-95"
    x-transition:enter-end="transform opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-75"
    x-transition:leave-start="transform opacity-100 scale-100"
    x-transition:leave-end="transform opacity-0 scale-95"
    class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
    role="menu">
    <div class="py-1" role="none">
      @forelse($notifications as $notification)
      <a href="#"
        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $notification->read_at ? '' : 'bg-blue-50' }}"
        role="menuitem">
        <p class="font-medium">{{ $notification->data['title'] ?? 'Notification' }}</p>
        <p class="text-gray-500">{{ $notification->data['message'] ?? '' }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
      </a>
      @empty
      <div class="px-4 py-2 text-sm text-gray-700">
        No notifications
      </div>
      @endforelse

      @if($notifications->isNotEmpty())
      <div class="border-t border-gray-100"></div>
      <a href="{{ route('notifications.index') }}"
        class="block px-4 py-2 text-sm text-blue-600 hover:bg-gray-100">
        View all notifications
      </a>
      @endif
    </div>
  </div>
</div>
