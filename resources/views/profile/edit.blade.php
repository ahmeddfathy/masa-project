<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between items-center">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Edit Profile') }}
      </h2>
      <a href="{{ route('profile.show') }}"
        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
        Back to Profile
      </a>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- Profile Information -->
      <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Profile Information</h3>
          <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name"
                  value="{{ old('name', $user->name) }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email"
                  value="{{ old('email', $user->email) }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="tel" name="phone" id="phone"
                  value="{{ old('phone', $user->phone) }}"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div class="sm:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea name="address" id="address" rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('address', $user->address) }}</textarea>
                @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="mt-6">
              <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Update Profile
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Update Password -->
      <div class="mt-8 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Update Password</h3>
          <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                <input type="password" name="current_password" id="current_password"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('current_password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="password" id="password"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
              </div>
            </div>

            <div class="mt-6">
              <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Update Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
