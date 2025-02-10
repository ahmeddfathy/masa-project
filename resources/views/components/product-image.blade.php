@props(['product', 'size' => '16'])

@if($product->primary_image)
    <img src="{{ Storage::url($product->primary_image->image_path) }}"
        alt="{{ $product->name }}"
        {{ $attributes->merge(['class' => "w-{$size} h-{$size} object-cover rounded"]) }}>
@else
    <div class="w-{{ $size }} h-{{ $size }} bg-gray-200 rounded flex items-center justify-center">
        <svg class="w-{{ (int)($size/2) }} h-{{ (int)($size/2) }} text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
@endif
