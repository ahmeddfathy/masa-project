<a href="{{ route('cart.index') }}"
  class="relative inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50">
  <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
  </svg>
  Cart
  @if($itemCount > 0)
  <span class="absolute -top-1 -right-1 bg-red-600 text-white rounded-full h-5 w-5 flex items-center justify-center text-xs">
    {{ $itemCount }}
  </span>
  @endif
</a>
