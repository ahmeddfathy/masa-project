@props(['user'])

<form action="{{ route('checkout.process') }}" method="POST" class="space-y-6">
    @csrf

    <!-- Contact Information -->
    <div>
        <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
        <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="tel" name="phone" id="phone"
                    value="{{ old('phone', $user->phone ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Shipping Address -->
    <div class="mt-6">
        <h3 class="text-lg font-medium text-gray-900">Shipping Address</h3>
        <div class="mt-4">
            <label for="shipping_address" class="block text-sm font-medium text-gray-700">Address</label>
            <textarea name="shipping_address" id="shipping_address" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('shipping_address', $user->address ?? '') }}</textarea>
            @error('shipping_address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Payment Method -->
    <div class="mt-6">
        <h3 class="text-lg font-medium text-gray-900">Payment Method</h3>
        <div class="mt-4 space-y-4">
            <div class="flex items-center">
                <input id="payment_method_cash" name="payment_method" type="radio" value="cash"
                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="payment_method_cash" class="ml-3 block text-sm font-medium text-gray-700">
                    Cash on Delivery
                </label>
            </div>
            <div class="flex items-center">
                <input id="payment_method_card" name="payment_method" type="radio" value="card"
                    class="h-4 w-4 border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="payment_method_card" class="ml-3 block text-sm font-medium text-gray-700">
                    Credit Card
                </label>
            </div>
            @error('payment_method')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="mt-6">
        <button type="submit"
            class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
            Place Order
        </button>
    </div>
</form>
