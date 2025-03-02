<?php

namespace App\Services\Customer\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductService
{
    public function getFilteredProducts(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'images', 'colors', 'sizes'])
            ->where('is_available', true)
            ->when($request->search, function (Builder $query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->category, function (Builder $query, $category) {
                $query->whereHas('category', function($q) use ($category) {
                    $q->where('slug', $category);
                });
            })
            ->when($request->max_price, function (Builder $query, $maxPrice) {
                $query->where('price', '<=', $maxPrice);
            });

        $query->when($request->sort, function (Builder $query, $sort) {
            match ($sort) {
                'price-low' => $query->orderBy('price', 'asc'),
                'price-high' => $query->orderBy('price', 'desc'),
                'newest' => $query->latest(),
                default => $query->orderBy('created_at', 'desc')
            };
        });

        return $query->paginate($request->per_page ?? 12);
    }

    public function getCategories()
    {
        return Category::select('id', 'name', 'slug')
            ->withCount(['products' => function($query) {
                $query->where('is_available', true);
            }])
            ->get();
    }

    public function getPriceRange()
    {
        return [
            'min' => Product::min('price'),
            'max' => Product::max('price')
        ];
    }

    public function formatProductsForJson($products)
    {
        return collect($products->items())->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category' => [
                    'name' => $product->category->name,
                    'slug' => $product->category->slug
                ],
                'price' => $product->price,
                'image_url' => $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('images/placeholder.jpg'),
                'images' => collect($product->images)->map(function($image) {
                    return asset('storage/' . $image->image_path);
                })->toArray(),
                'colors' => collect($product->colors)->map(function($color) {
                    return [
                        'name' => $color->color,
                        'is_available' => $color->is_available
                    ];
                })->toArray(),
                'sizes' => collect($product->sizes)->map(function($size) {
                    return [
                        'name' => $size->size,
                        'is_available' => $size->is_available,
                        'price' => $size->price ?? null
                    ];
                })->toArray(),
                'rating' => $product->rating ?? 0,
                'reviews' => $product->reviews ?? 0,
                'is_available' => $product->stock > 0
            ];
        });
    }

    public function formatProductsForFilter($products)
    {
        return collect($products->items())->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category' => $product->category->name,
                'price' => number_format($product->price, 2),
                'image_url' => $product->images->first() ?
                    asset('storage/' . $product->images->first()->image_path) :
                    asset('images/placeholder.jpg'),
                'rating' => $product->rating ?? 0,
                'reviews' => $product->reviews ?? 0,
                'is_available' => $product->stock > 0,
                'description' => Str::limit($product->description, 100)
            ];
        });
    }

    public function getProductDetails(Product $product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => $product->price,
            'category' => $product->category->name,
            'image_url' => $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('images/placeholder.jpg'),
            'images' => collect($product->images)->map(function($image) {
                return asset('storage/' . $image->image_path);
            })->toArray(),
            'colors' => $product->allow_color_selection ? collect($product->colors)->map(function($color) {
                return [
                    'name' => $color->color,
                    'is_available' => $color->is_available
                ];
            })->toArray() : [],
            'sizes' => $product->allow_size_selection ? collect($product->sizes)->map(function($size) {
                return [
                    'name' => $size->size,
                    'is_available' => $size->is_available
                ];
            })->toArray() : [],
            'is_available' => $product->stock > 0,
            'features' => [
                'allow_custom_color' => $product->allow_custom_color,
                'allow_custom_size' => $product->allow_custom_size,
                'allow_color_selection' => $product->allow_color_selection,
                'allow_size_selection' => $product->allow_size_selection,
                'allow_appointment' => $product->allow_appointment
            ]
        ];
    }

    public function getRelatedProducts(Product $product)
    {
        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->with(['category', 'images'])
            ->take(4)
            ->get();
    }

    public function getAvailableFeatures(Product $product)
    {
        $availableFeatures = [];

        if ($product->allow_color_selection && $product->colors->isNotEmpty()) {
            $availableFeatures[] = [
                'icon' => 'palette',
                'text' => 'يمكنك اختيار لون من الألوان المتاحة'
            ];
        }

        if ($product->allow_custom_color) {
            $availableFeatures[] = [
                'icon' => 'paint-brush',
                'text' => 'يمكنك إضافة لون مخصص حسب رغبتك'
            ];
        }

        if ($product->allow_size_selection && $product->sizes->isNotEmpty()) {
            $availableFeatures[] = [
                'icon' => 'ruler',
                'text' => 'يمكنك اختيار مقاس من المقاسات المتاحة'
            ];
        }

        if ($product->allow_custom_size) {
            $availableFeatures[] = [
                'icon' => 'ruler-combined',
                'text' => 'يمكنك إضافة مقاس مخصص حسب رغبتك'
            ];
        }

        // فحص ما إذا كانت ميزة مواعيد المتجر مفعلة في الإعدادات
        $showStoreAppointments = \App\Models\Setting::getBool('show_store_appointments', true);

        if ($product->allow_appointment && $showStoreAppointments) {
            $availableFeatures[] = [
                'icon' => 'tape',
                'text' => 'يمكنك طلب موعد لأخذ المقاسات'
            ];
        }

        return $availableFeatures;
    }
}
