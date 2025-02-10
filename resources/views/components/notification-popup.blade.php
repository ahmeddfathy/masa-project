<div x-data="{ show: true }"
  x-show="show"
  x-init="setTimeout(() => show = false, 5000)"
  class="fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 max-w-sm w-full">
  <div class="flex items-start">
    <div class="flex-shrink-0">
      @if($notification->data['type'] ?? '' === 'success')
      <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      @else
      <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      @endif
    </div>
    <div class="ml-3 w-0 flex-1">
      <p class="text-sm font-medium text-gray-900">
        {{ $notification->data['title'] ?? 'Notification' }}
      </p>
      <p class="mt-1 text-sm text-gray-500">
        {{ $notification->data['message'] ?? '' }}
      </p>
    </div>
    <div class="ml-4 flex-shrink-0 flex">
      <button @click="show = false"
        class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
        <span class="sr-only">Close</span>
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>
  </div>
</div>
