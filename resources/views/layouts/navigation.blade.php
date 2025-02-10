<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
  <!-- Primary Navigation Menu -->
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex">
        <!-- Logo -->
        <div class="shrink-0 flex items-center">
          <a href="{{ route('home') }}">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
          </a>
        </div>

        <!-- Navigation Links -->
        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
          <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
          </x-nav-link>
          <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
            {{ __('Products') }}
          </x-nav-link>
          <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">
            {{ __('Appointments') }}
          </x-nav-link>
        </div>
      </div>

      <!-- Cart Indicator -->
      <div class="hidden sm:flex sm:items-center sm:ml-6">
        <x-cart-indicator />

        <!-- Notifications -->
        <div class="ml-3 relative">
          <x-notifications />
        </div>

        <!-- User Dropdown -->
        <div class="ml-3 relative">
          @auth
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                <div>{{ Auth::user()->name }}</div>
                <div class="ml-1">
                  <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </div>
              </button>
            </x-slot>

            <x-slot name="content">
              <x-dropdown-link :href="route('profile.edit')">
                {{ __('Profile') }}
              </x-dropdown-link>
              <x-dropdown-link :href="route('orders.index')">
                {{ __('My Orders') }}
              </x-dropdown-link>
              <x-dropdown-link :href="route('appointments.index')">
                {{ __('My Appointments') }}
              </x-dropdown-link>

              <!-- Authentication -->
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                  onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                  {{ __('Log Out') }}
                </x-dropdown-link>
              </form>
            </x-slot>
          </x-dropdown>
          @else
          <div class="space-x-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900">Login</a>
            <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:text-gray-900">Register</a>
          </div>
          @endauth
        </div>
      </div>
    </div>
  </div>
</nav>
