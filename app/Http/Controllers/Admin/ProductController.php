<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images'])
            ->withCount('orderItems');

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
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
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
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
            $rules['size_available'] = 'array';
            $rules['size_available.*'] = 'boolean';
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
            $validatedData['is_available'] = $request->has('is_available');
            $validatedData['slug'] = Str::slug($validatedData['name']);

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
            if ($request->has('has_sizes') && $request->has('sizes')) {
                foreach ($request->sizes as $index => $size) {
                    if (!empty($size)) {
                        $product->sizes()->create([
                            'size' => $size,
                            'is_available' => $request->size_available[$index] ?? true
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
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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
            }

            $validated = $request->validate($rules);

            DB::beginTransaction();

            // Update feature flags
            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'category_id' => $validated['category_id'],
                'is_available' => $request->has('is_available'),
                'enable_custom_color' => $request->has('enable_custom_color'),
                'enable_custom_size' => $request->has('enable_custom_size'),
                'enable_color_selection' => $request->has('enable_color_selection'),
                'enable_size_selection' => $request->has('enable_size_selection'),
                'enable_appointments' => $request->has('enable_appointments'),
                'slug' => Str::slug($validated['name'])
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

            DB::commit();
            return redirect()->route('admin.products.index')
                ->with('success', 'تم تحديث المنتج بنجاح');
        } catch (\Exception $e) {
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
