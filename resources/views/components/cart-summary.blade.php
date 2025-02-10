@props(['total', 'showCheckoutButton' => true])

<div class="bg-gray-50 p-6 rounded-lg">
    <h2 class="text-lg font-medium text-gray-900">Order Summary</h2>

    <div class="mt-6 space-y-4">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600">Subtotal</p>
            <p class="text-sm font-medium text-gray-900">{{ number_format($total / 100, 2) }} SAR</p>
        </div>

        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600">Shipping</p>
            <p class="text-sm font-medium text-gray-900">Free</p>
        </div>

        <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
            <p class="text-base font-medium text-gray-900">Total</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($total / 100, 2) }} SAR</p>
        </div>
    </div>

    @if($showCheckoutButton)
        <div class="mt-6">
            <a href="{{ route('checkout.index') }}"
                class="w-full flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">
                Proceed to Checkout
            </a>
        </div>
    @endif
</div>
