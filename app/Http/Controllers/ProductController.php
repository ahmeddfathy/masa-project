<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchProductsRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
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

        // Apply sorting
        $query->when($request->sort, function (Builder $query, $sort) {
            match ($sort) {
                'price-low' => $query->orderBy('price', 'asc'),
                'price-high' => $query->orderBy('price', 'desc'),
                'newest' => $query->latest(),
                default => $query->orderBy('created_at', 'desc')
            };
        });

        $products = $query->paginate($request->per_page ?? 12);

        $categories = Category::select('id', 'name', 'slug')
            ->withCount('products')
            ->get();

        // Get price range for filter
        $priceRange = [
            'min' => Product::min('price'),
            'max' => Product::max('price')
        ];

        if ($request->ajax()) {
            return response()->json([
                'products' => collect($products->items())->map(function($product) {
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
                                'is_available' => $size->is_available
                            ];
                        })->toArray(),
                        'rating' => $product->rating ?? 0,
                        'reviews' => $product->reviews ?? 0
                    ];
                }),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total()
                ]
            ]);
        }

        return view('products.index', compact('products', 'categories', 'priceRange'));
    }

    public function show(Product $product)
    {
        if (!$product->is_available) {
            abort(404, 'المنتج غير متوفر حالياً');
        }

        $product->load(['category', 'images', 'colors', 'sizes']);

        // Get related products from same category (only available ones)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->with(['category', 'images'])
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function filter(Request $request)
    {
        try {
            $query = Product::with(['category', 'images', 'colors', 'sizes'])
                ->where('is_available', true);

            // Filter by categories
            if ($request->has('categories') && !empty($request->categories)) {
                $query->whereHas('category', function($q) use ($request) {
                    $q->whereIn('slug', $request->categories);
                });
            }

            // Filter by price range
            if ($request->has('minPrice') && is_numeric($request->minPrice)) {
                $query->where('price', '>=', (float) $request->minPrice);
            }
            if ($request->has('maxPrice') && is_numeric($request->maxPrice)) {
                $query->where('price', '<=', (float) $request->maxPrice);
            }

            // Apply sorting
            if ($request->has('sort')) {
                match ($request->sort) {
                    'price-low' => $query->orderBy('price', 'asc'),
                    'price-high' => $query->orderBy('price', 'desc'),
                    'newest' => $query->latest(),
                    default => $query->latest()
                };
            } else {
                $query->latest();
            }

            $products = $query->paginate($request->per_page ?? 12);

            return response()->json([
                'success' => true,
                'products' => collect($products->items())->map(function($product) {
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
                        'stock' => $product->stock,
                        'description' => Str::limit($product->description, 100)
                    ];
                }),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المنتجات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProductDetails(Product $product)
    {
        if (!$product->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير متوفر حالياً'
            ], 404);
        }

        $product->load(['category', 'images', 'colors', 'sizes']);

        return response()->json([
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
            'colors' => collect($product->colors)->map(function($color) {
                return [
                    'name' => $color->color,
                    'is_available' => $color->is_available
                ];
            })->toArray(),
            'sizes' => collect($product->sizes)->map(function($size) {
                return [
                    'name' => $size->size,
                    'is_available' => $size->is_available
                ];
            })->toArray(),
            'stock' => $product->stock > 0 ? 'متوفر' : 'غير متوفر'
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'color' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:50',
            'needs_appointment' => 'required|boolean'
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، هذا المنتج غير متاح حالياً'
            ], 422);
        }

        // Check if appointment is needed
        $needs_appointment = $request->needs_appointment;

        // تعديل التحقق من المقاس ليكون أكثر مرونة
        if (!$needs_appointment) {
            if ($product->sizes()->count() > 0 && !$request->size) {
                return response()->json([
                    'success' => false,
                    'message' => 'يرجى اختيار المقاس أو تحديد موعد لأخذ المقاسات'
                ], 422);
            }
        }

        // Get or create cart
        $cart = null;
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => Str::random(40)]
            );
        } else {
            $sessionId = $request->session()->get('cart_session_id');
            if (!$sessionId) {
                $sessionId = Str::random(40);
                $request->session()->put('cart_session_id', $sessionId);
            }
            $cart = Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['total_amount' => 0]
            );
        }

        // تعديل التحقق من العنصر في السلة ليشمل الحالة الجديدة
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('needs_appointment', $needs_appointment)
            ->where(function($query) use ($request) {
                $query->where('color', $request->color)
                      ->orWhereNull('color');
            })
            ->where(function($query) use ($request) {
                $query->where('size', $request->size)
                      ->orWhereNull('size');
            })
            ->first();

        if ($cartItem) {
            // Update existing cart item
            $cartItem->quantity += $request->quantity;
            $cartItem->subtotal = $cartItem->quantity * $product->price;
            $cartItem->save();
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'subtotal' => $request->quantity * $product->price,
                'color' => $request->color,
                'size' => $request->size,
                'needs_appointment' => $needs_appointment
            ]);
        }

        // Update cart total
        $cart->total_amount = $cart->items()->sum('subtotal');
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة المنتج إلى سلة التسوق',
            'cart_count' => $cart->items()->sum('quantity'),
            'cart_total' => $cart->total_amount,
            'show_appointment' => $needs_appointment,
            'product_name' => $product->name,
            'product_id' => $product->id,
            'cart_item_id' => $cartItem->id,
            'show_modal' => $needs_appointment
        ]);
    }

    public function getCartItems(Request $request)
    {
        $cart = null;
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
        } else {
            $sessionId = $request->session()->get('cart_session_id');
            if ($sessionId) {
                $cart = Cart::where('session_id', $sessionId)->first();
            }
        }

        if (!$cart) {
            return response()->json([
                'items' => [],
                'total' => 0,
                'count' => 0
            ]);
        }

        $items = $cart->items()->with('product.images')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'image' => $item->product->images->first() ?
                    asset('storage/' . $item->product->images->first()->image_path) :
                    asset('images/placeholder.jpg'),
                'quantity' => $item->quantity,
                'price' => $item->unit_price,
                'subtotal' => $item->subtotal,
            ];
        });

        return response()->json([
            'items' => $items,
            'total' => $cart->total_amount,
            'count' => $cart->items()->sum('quantity')
        ]);
    }

    public function updateCartItem(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem->quantity = $request->quantity;
        $cartItem->subtotal = $cartItem->quantity * $cartItem->unit_price;
        $cartItem->save();

        // Update cart total
        $cart = $cartItem->cart;
        $cart->total_amount = $cart->items()->sum('subtotal');
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الكمية بنجاح',
            'item_subtotal' => $cartItem->subtotal,
            'cart_total' => $cart->total_amount,
            'cart_count' => $cart->items()->sum('quantity')
        ]);
    }

    public function removeCartItem(CartItem $cartItem)
    {
        try {
            // التحقق من أن العنصر ينتمي للمستخدم الحالي
            if ($cartItem->cart->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح بهذا الإجراء'
                ], 403);
            }

            $cart = $cartItem->cart;
            $cartItem->delete();

            // تحديث إجمالي السلة
            $cart->updateTotals();

            // إرجاع البيانات المحدثة للسلة
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج من السلة بنجاح',
                'count' => $cart->items->count(),
                'total' => number_format($cart->total_amount, 2) . ' ر.س',
                'items' => $cart->items->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->product->name,
                        'price' => number_format($item->price, 2),
                        'quantity' => $item->quantity,
                        'subtotal' => number_format($item->subtotal, 2),
                        'image' => $item->product->image_url
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنتج'
            ], 500);
        }
    }
}
