<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductQuantity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'sizes', 'quantities'])
            ->withCount('orderItems');

        // Filter by specific product
        if ($request->product) {
            $query->where('id', $request->product);
        }

        // Filter by category
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by search term
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Handle sorting
        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'price_high':
                // Order by the maximum price of sizes or quantities
                $query->orderBy(function($q) {
                    return $q->select(DB::raw('MAX(COALESCE(ps.price, pq.price, 0))'))
                        ->from('products as p')
                        ->leftJoin('product_sizes as ps', 'p.id', '=', 'ps.product_id')
                        ->leftJoin('product_quantities as pq', 'p.id', '=', 'pq.product_id')
                        ->whereColumn('p.id', 'products.id')
                        ->limit(1);
                }, 'desc');
                break;
            case 'price_low':
                // Order by the minimum price of sizes or quantities
                $query->orderBy(function($q) {
                    return $q->select(DB::raw('MIN(COALESCE(ps.price, pq.price, 0))'))
                        ->from('products as p')
                        ->leftJoin('product_sizes as ps', 'p.id', '=', 'ps.product_id')
                        ->leftJoin('product_quantities as pq', 'p.id', '=', 'pq.product_id')
                        ->whereColumn('p.id', 'products.id')
                        ->limit(1);
                });
                break;
            default:
                $query->latest(); // 'newest' is default
                break;
        }

        $products = $query->paginate(15);
        $categories = Category::all();
        $allProducts = Product::orderBy('name')->get(); // Get all products sorted by name

        return view('admin.products.index', compact('products', 'categories', 'allProducts'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Basic validation rules that are always required
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'description' => 'required|string',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg',
            'is_primary.*' => 'boolean',
            'is_available' => 'boolean',
            'enable_custom_color' => 'boolean',
            'enable_custom_size' => 'boolean',
            'enable_color_selection' => 'boolean',
            'enable_size_selection' => 'boolean',
            'enable_appointments' => 'boolean',
        ];

        // Add color validation rules only if colors are enabled
        if ($request->has('has_colors')) {
            $rules['colors'] = 'required|array|min:1';
            $rules['colors.*'] = 'required|string|max:255';
            $rules['color_available'] = 'array';
            $rules['color_available.*'] = 'boolean';
        }

        // Add size validation rules only if sizes are enabled
        if ($request->has('has_sizes')) {
            $rules['sizes'] = 'required|array|min:1';
            $rules['sizes.*'] = 'required|string|max:255';
            $rules['size_ids.*'] = 'nullable|exists:product_sizes,id';
            $rules['size_available.*'] = 'nullable|boolean';
            $rules['size_prices.*'] = 'nullable|numeric|min:0';
        }

        $validatedData = $request->validate($rules);

        try {
            DB::beginTransaction();

            // Set default values for feature flags
            $validatedData['enable_custom_color'] = $request->has('enable_custom_color');
            $validatedData['enable_custom_size'] = $request->has('enable_custom_size');
            $validatedData['enable_color_selection'] = $request->has('enable_color_selection');
            $validatedData['enable_size_selection'] = $request->has('enable_size_selection');
            $validatedData['enable_appointments'] = $request->has('enable_appointments');
            $validatedData['enable_quantity_pricing'] = $request->has('enable_quantity_pricing');
            $validatedData['is_available'] = $request->has('is_available');

            $product = Product::create($validatedData);

            // Store colors if enabled
            if ($request->has('has_colors') && $request->has('colors')) {
                foreach ($request->colors as $index => $color) {
                    if (!empty($color)) {
                        $product->colors()->create([
                            'color' => $color,
                            'is_available' => $request->color_available[$index] ?? true
                        ]);
                    }
                }
            }

            // Store sizes if enabled
            if ($request->enable_size_selection && isset($request->sizes)) {
                foreach ($request->sizes as $index => $size) {
                    if (!empty($size)) {
                        $price = null;
                        if (isset($request->size_prices[$index]) && !empty($request->size_prices[$index])) {
                            $price = $request->size_prices[$index];
                        }

                        $product->sizes()->create([
                            'size' => $size,
                            'price' => $price,
                            'is_available' => isset($request->size_available[$index]) ? 1 : 0
                        ]);
                    }
                }
            }

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $this->uploadFile($image, 'products');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_primary' => $request->input('is_primary.' . $index, false)
                    ]);
                }
            }

            // Store quantities if enabled
            if ($request->has('enable_quantity_pricing') && $request->has('quantities')) {
                foreach ($request->quantities as $index => $quantity) {
                    if (!empty($quantity) && isset($request->quantity_prices[$index])) {
                        $product->quantities()->create([
                            'quantity_value' => $quantity,
                            'price' => $request->quantity_prices[$index],
                            'description' => $request->quantity_descriptions[$index] ?? null,
                            'is_available' => in_array($index, $request->quantity_available ?? [])
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', 'تم إضافة المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'فشل إضافة المنتج. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'colors', 'sizes']);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            // Basic validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('products')->ignore($product->id)
                ],
                'description' => 'required|string',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg',
                'is_primary' => 'nullable|exists:product_images,id',
                'is_primary_new.*' => 'nullable|boolean',
                'remove_images.*' => 'nullable|exists:product_images,id',
                'enable_custom_color' => 'boolean',
                'enable_custom_size' => 'boolean',
                'enable_color_selection' => 'boolean',
                'enable_size_selection' => 'boolean',
                'enable_appointments' => 'boolean',
            ];

            // Add color validation rules only if colors are enabled
            if ($request->has('has_colors')) {
                $rules['colors'] = 'required|array|min:1';
                $rules['colors.*'] = 'required|string|max:255';
                $rules['color_ids.*'] = 'nullable|exists:product_colors,id';
                $rules['color_available.*'] = 'nullable|boolean';
            }

            // Add size validation rules only if sizes are enabled
            if ($request->has('has_sizes')) {
                $rules['sizes'] = 'required|array|min:1';
                $rules['sizes.*'] = 'required|string|max:255';
                $rules['size_ids.*'] = 'nullable|exists:product_sizes,id';
                $rules['size_available.*'] = 'nullable|boolean';
                $rules['size_prices.*'] = 'nullable|numeric|min:0';
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();

            $product->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'stock' => $validated['stock'],
                'category_id' => $validated['category_id'],
                'enable_custom_color' => $request->has('enable_custom_color'),
                'enable_custom_size' => $request->has('enable_custom_size'),
                'enable_color_selection' => $request->has('enable_color_selection'),
                'enable_size_selection' => $request->has('enable_size_selection'),
                'enable_appointments' => $request->has('enable_appointments'),
                'enable_quantity_pricing' => $request->has('enable_quantity_pricing'),
                'is_available' => $request->has('is_available'),
            ]);

            // Handle colors
            if ($request->has('has_colors')) {
                // Delete colors that are not in the new list
                $currentColorIds = $product->colors->pluck('id')->toArray();
                $updatedColorIds = array_filter($request->color_ids ?? []);
                $deletedColorIds = array_diff($currentColorIds, $updatedColorIds);

                if (!empty($deletedColorIds)) {
                    $product->colors()->whereIn('id', $deletedColorIds)->delete();
                }

                // Update or create colors
                foreach ($request->colors as $index => $colorName) {
                    if (!empty($colorName)) {
                        $colorId = $request->color_ids[$index] ?? null;
                        $colorData = [
                            'color' => $colorName,
                            'is_available' => $request->color_available[$index] ?? true
                        ];

                        if ($colorId && in_array($colorId, $currentColorIds)) {
                            $product->colors()->where('id', $colorId)->update($colorData);
                        } else {
                            $product->colors()->create($colorData);
                        }
                    }
                }
            } else {
                $product->colors()->delete();
            }

            // Handle sizes
            if ($request->has('has_sizes')) {
                // Delete sizes that are not in the new list
                $currentSizeIds = $product->sizes->pluck('id')->toArray();
                $updatedSizeIds = array_filter($request->size_ids ?? []);
                $deletedSizeIds = array_diff($currentSizeIds, $updatedSizeIds);

                if (!empty($deletedSizeIds)) {
                    $product->sizes()->whereIn('id', $deletedSizeIds)->delete();
                }

                // Update or create sizes
                foreach ($request->sizes as $index => $sizeName) {
                    if (!empty($sizeName)) {
                        $sizeId = $request->size_ids[$index] ?? null;
                        $sizeData = [
                            'size' => $sizeName,
                            'is_available' => $request->size_available[$index] ?? true
                        ];

                        // Add price to size data if provided
                        if (isset($request->size_prices[$index])) {
                            $sizeData['price'] = $request->size_prices[$index];
                        }

                        if ($sizeId && in_array($sizeId, $currentSizeIds)) {
                            $product->sizes()->where('id', $sizeId)->update($sizeData);
                        } else {
                            $product->sizes()->create($sizeData);
                        }
                    }
                }
            } else {
                $product->sizes()->delete();
            }

            // Handle image removals
            if ($request->has('remove_images')) {
                foreach ($request->remove_images as $imageId) {
                    $image = $product->images()->find($imageId);
                    if ($image) {
                        $this->deleteFile($image->image_path);
                        $image->delete();
                    }
                }
            }

            // Handle new images
            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $index => $image) {
                    $path = $this->uploadFile($image, 'products');
                    $product->images()->create([
                        'image_path' => $path,
                        'is_primary' => $request->input('is_primary_new.' . $index, false)
                    ]);
                }
            }

            // Update primary image
            if ($request->has('is_primary')) {
                $product->images()->update(['is_primary' => false]);
                $product->images()->where('id', $request->is_primary)->update(['is_primary' => true]);
            }

            // Handle quantities
            if ($request->has('enable_quantity_pricing')) {
                // Update existing quantities
                if ($request->has('quantities')) {
                    foreach ($request->quantities as $index => $quantityValue) {
                        if (!empty($quantityValue) && isset($request->quantity_prices[$index])) {
                            $quantityId = $request->quantity_ids[$index] ?? null;

                            if ($quantityId) {
                                $quantity = ProductQuantity::find($quantityId);
                                if ($quantity) {
                                    $quantity->update([
                                        'quantity_value' => $quantityValue,
                                        'price' => $request->quantity_prices[$index],
                                        'description' => $request->quantity_descriptions[$index] ?? null,
                                        'is_available' => in_array($index, $request->quantity_available ?? [])
                                    ]);
                                }
                            } else {
                                $product->quantities()->create([
                                    'quantity_value' => $quantityValue,
                                    'price' => $request->quantity_prices[$index],
                                    'description' => $request->quantity_descriptions[$index] ?? null,
                                    'is_available' => in_array($index, $request->quantity_available ?? [])
                                ]);
                            }
                        }
                    }
                }
            } else {
                $product->quantities()->delete();
            }

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', 'تم تحديث المنتج بنجاح');
        } catch (\Exception $e) {
            Log::error('Product update error: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            DB::rollBack();
            return back()->withInput()
                ->with('error', 'فشل تحديث المنتج. ' . $e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Delete all associated records first
            $product->colors()->delete();
            $product->sizes()->delete();
            $product->orderItems()->delete();

            // Delete all associated images and their files
            foreach ($product->images as $image) {
                $this->deleteFile($image->image_path);
                $image->delete();
            }

            // Finally delete the product
            $product->forceDelete(); // not needed anymore since we removed SoftDeletes, but kept for clarity

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', 'تم حذف المنتج بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'فشل حذف المنتج. ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'images', 'colors', 'sizes', 'orderItems']);
        return view('admin.products.show', compact('product'));
    }

    protected function getValidationRules(): array
    {
        return [
            // ... existing validation rules ...
            'enable_custom_color' => 'boolean',
            'enable_custom_size' => 'boolean',
            'enable_color_selection' => 'boolean',
            'enable_size_selection' => 'boolean',
            'enable_appointments' => 'boolean',
        ];
    }

    protected function prepareForValidation($data)
    {
        // Convert checkbox values to boolean
        $checkboxFields = [
            'enable_custom_color',
            'enable_custom_size',
            'enable_color_selection',
            'enable_size_selection',
            'enable_appointments'
        ];

        foreach ($checkboxFields as $field) {
            $data[$field] = isset($data[$field]) && $data[$field] === 'on';
        }

        return $data;
    }
}
