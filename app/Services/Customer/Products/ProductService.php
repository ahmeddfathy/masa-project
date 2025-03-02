<?php

namespace App\Services\Customer\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getFilteredProducts(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'images', 'colors', 'sizes', 'quantities'])
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
                // فلترة حسب السعر الأقصى باستخدام استعلام فرعي
                $query->where(function($q) use ($maxPrice) {
                    // المنتجات التي لها مقاسات بأسعار أقل من أو تساوي الحد الأقصى
                    $q->whereExists(function($subQuery) use ($maxPrice) {
                        $subQuery->select(DB::raw(1))
                            ->from('product_sizes')
                            ->whereColumn('product_sizes.product_id', 'products.id')
                            ->where('product_sizes.price', '<=', $maxPrice);
                    });

                    // أو المنتجات التي لها كميات بأسعار أقل من أو تساوي الحد الأقصى
                    $q->orWhereExists(function($subQuery) use ($maxPrice) {
                        $subQuery->select(DB::raw(1))
                            ->from('product_quantities')
                            ->whereColumn('product_quantities.product_id', 'products.id')
                            ->where('product_quantities.price', '<=', $maxPrice);
                    });
                });
            });

        $query->when($request->sort, function (Builder $query, $sort) {
            match ($sort) {
                'price-low' => $query->orderBy(function($q) {
                    return $q->select(DB::raw('MIN(COALESCE(ps.price, pq.price, 0))'))
                        ->from('products as p')
                        ->leftJoin('product_sizes as ps', 'p.id', '=', 'ps.product_id')
                        ->leftJoin('product_quantities as pq', 'p.id', '=', 'pq.product_id')
                        ->whereColumn('p.id', 'products.id')
                        ->limit(1);
                }),
                'price-high' => $query->orderBy(function($q) {
                    return $q->select(DB::raw('MAX(COALESCE(ps.price, pq.price, 0))'))
                        ->from('products as p')
                        ->leftJoin('product_sizes as ps', 'p.id', '=', 'ps.product_id')
                        ->leftJoin('product_quantities as pq', 'p.id', '=', 'pq.product_id')
                        ->whereColumn('p.id', 'products.id')
                        ->limit(1);
                }, 'desc'),
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
        // Determine the min and max prices from all available products
        $minPrice = DB::table('products as p')
            ->leftJoin('product_sizes as ps', 'p.id', '=', 'ps.product_id')
            ->leftJoin('product_quantities as pq', 'p.id', '=', 'pq.product_id')
            ->where('p.is_available', true)
            ->min(DB::raw('LEAST(COALESCE(ps.price, 999999), COALESCE(pq.price, 999999))'));

        $maxPrice = DB::table('products as p')
            ->leftJoin('product_sizes as ps', 'p.id', '=', 'ps.product_id')
            ->leftJoin('product_quantities as pq', 'p.id', '=', 'pq.product_id')
            ->where('p.is_available', true)
            ->max(DB::raw('GREATEST(COALESCE(ps.price, 0), COALESCE(pq.price, 0))'));

        return [
            'min' => $minPrice ?: 0,
            'max' => $maxPrice ?: 0
        ];
    }

    public function formatProductsForJson($products)
    {
        return collect($products->items())->map(function($product) {
            $priceRange = $product->getPriceRange();

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category' => [
                    'name' => $product->category->name,
                    'slug' => $product->category->slug
                ],
                'price_range' => [
                    'min' => $priceRange['min'],
                    'max' => $priceRange['max']
                ],
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
            $priceRange = $product->getPriceRange();
            $priceDisplay = $priceRange['min'] == $priceRange['max']
                ? number_format($priceRange['min'], 2)
                : number_format($priceRange['min'], 2) . ' - ' . number_format($priceRange['max'], 2);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category' => $product->category->name,
                'price_display' => $priceDisplay . ' ر.س',
                'price_range' => [
                    'min' => $priceRange['min'],
                    'max' => $priceRange['max']
                ],
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
        $priceRange = $product->getPriceRange();

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price_range' => [
                'min' => $priceRange['min'],
                'max' => $priceRange['max']
            ],
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
                    'is_available' => $size->is_available,
                    'price' => $size->price
                ];
            })->toArray() : [],
            'quantities' => $product->enable_quantity_pricing ? collect($product->quantities)->map(function($quantity) {
                return [
                    'id' => $quantity->id,
                    'value' => $quantity->quantity_value,
                    'price' => $quantity->price,
                    'description' => $quantity->description,
                    'is_available' => $quantity->is_available
                ];
            })->toArray() : [],
            'is_available' => $product->stock > 0,
            'features' => [
                'allow_custom_color' => $product->allow_custom_color,
                'allow_custom_size' => $product->allow_custom_size,
                'allow_color_selection' => $product->allow_color_selection,
                'allow_size_selection' => $product->allow_size_selection,
                'allow_appointment' => $product->allow_appointment,
                'enable_quantity_pricing' => $product->enable_quantity_pricing
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
