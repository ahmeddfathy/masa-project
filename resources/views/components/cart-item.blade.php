@props(['item', 'product', 'quantity'])

<tr>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            @if($product->primary_image)
                <img src="{{ Storage::url($product->primary_image->image_path) }}"
                    alt="{{ $product->name }}"
                    class="w-16 h-16 object-cover rounded">
            @else
                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
            <div class="ml-4">
                <div class="text-sm font-medium text-gray-900">
                    {{ $product->name }}
                </div>
                <p class="text-sm text-gray-500">{{ $product->category->name }}</p>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <form action="{{ route('cart.update', $product) }}" method="POST" class="flex items-center justify-center">
            @csrf
            @method('PATCH')
            <div class="custom-number-input">
                <div class="flex flex-row h-8 w-24 rounded-lg relative bg-transparent">
                    <button type="button"
                        onclick="this.parentNode.querySelector('input[type=number]').stepDown(); this.form.submit()"
                        class="bg-gray-100 text-gray-600 hover:text-gray-700 hover:bg-gray-200 h-full w-20 rounded-l cursor-pointer">
                        <span class="m-auto text-xl font-bold">−</span>
                    </button>
                    <input type="number"
                        name="quantity"
                        value="{{ $quantity }}"
                        min="1"
                        max="{{ $product->stock }}"
                        class="w-full text-center bg-gray-50 font-semibold text-md hover:text-black focus:text-black md:text-base cursor-default flex items-center text-gray-700 outline-none"
                        onchange="this.form.submit()">
                    <button type="button"
                        onclick="this.parentNode.querySelector('input[type=number]').stepUp(); this.form.submit()"
                        class="bg-gray-100 text-gray-600 hover:text-gray-700 hover:bg-gray-200 h-full w-20 rounded-r cursor-pointer">
                        <span class="m-auto text-xl font-bold">+</span>
                    </button>
                </div>
            </div>
        </form>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
        {{ number_format($product->price / 100, 2) }} SAR
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
        {{ number_format(($product->price * $quantity) / 100, 2) }} SAR
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <form action="{{ route('cart.remove', $product) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                class="text-red-600 hover:text-red-900"
                onclick="return confirm('Are you sure you want to remove this item?')">
                Remove
            </button>
        </form>
    </td>
</tr>
