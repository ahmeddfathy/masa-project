<footer class="bg-white border-t border-gray-200">
  <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <!-- Company Info -->
      <div class="col-span-1">
        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
          {{ config('app.name') }}
        </h3>
        <p class="mt-4 text-base text-gray-500">
          Your premier destination for exclusive abayas and tailoring services.
        </p>
      </div>

      <!-- Quick Links -->
      <div class="col-span-1">
        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
          Quick Links
        </h3>
        <ul class="mt-4 space-y-4">
          <li>
            <a href="{{ route('products.index') }}" class="text-base text-gray-500 hover:text-gray-900">
              Products
            </a>
          </li>
          <li>
            <a href="{{ route('appointments.create') }}" class="text-base text-gray-500 hover:text-gray-900">
              Book Appointment
            </a>
          </li>
        </ul>
      </div>

      <!-- Customer Service -->
      <div class="col-span-1">
        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
          Customer Service
        </h3>
        <ul class="mt-4 space-y-4">
          <li>
            <a href="{{ route('contact') }}" class="text-base text-gray-500 hover:text-gray-900">
              Contact Us
            </a>
          </li>
          <li>
            <a href="{{ route('faq') }}" class="text-base text-gray-500 hover:text-gray-900">
              FAQ
            </a>
          </li>
        </ul>
      </div>

      <!-- Contact Info -->
      <div class="col-span-1">
        <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
          Contact Us
        </h3>
        <ul class="mt-4 space-y-4">
          <li class="flex items-center text-gray-500">
            <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            +966 123 456 789
          </li>
          <li class="flex items-center text-gray-500">
            <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            contact@example.com
          </li>
        </ul>
      </div>
    </div>

    <div class="mt-8 border-t border-gray-200 pt-8">
      <p class="text-base text-gray-400 text-center">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
      </p>
    </div>
  </div>
</footer>
