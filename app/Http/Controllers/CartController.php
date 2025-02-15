<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;

class CartController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول للوصول إلى سلة التسوق');
        }

        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return view('cart.index', [
                'cart_items' => collect(),
                'subtotal' => 0,
                'total' => 0
            ]);
        }

        $cart_items = $cart->items()
            ->with(['product.images', 'product.category'])
            ->get();

        $subtotal = $cart_items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $total = $subtotal;

        return view('cart.index', compact(
            'cart_items',
            'subtotal',
            'total'
        ));
    }

    public function add(Request $request, Product $product)
    {
        // للمستخدمين المسجلين
        if (Auth::check()) {
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);

            $cartItem = $cart->items()->where('product_id', $product->id)->first();

            if ($cartItem) {
                $cartItem->increment('quantity');
                $cartItem->update([
                    'subtotal' => $cartItem->quantity * $cartItem->unit_price
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'unit_price' => $product->price,
                    'subtotal' => $product->price
                ]);
            }

            // تحديث إجمالي السلة
            $cart->update([
                'total_amount' => $cart->items->sum('subtotal')
            ]);
        }
        // للزوار
        else {
            $cart = Session::get('cart', []);
            if (isset($cart[$product->id])) {
                $cart[$product->id]++;
            } else {
                $cart[$product->id] = 1;
            }
            Session::put('cart', $cart);
        }

        return back()->with('success', 'Product added to cart successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Session::get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id] = $request->quantity;
            Session::put('cart', $cart);
            return back()->with('success', 'Cart updated successfully.');
        }

        return back()->with('error', 'Product not found in cart.');
    }

    public function remove(Product $product)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            Session::put('cart', $cart);
            return back()->with('success', 'Product removed from cart.');
        }

        return back()->with('error', 'Product not found in cart.');
    }

    public function clear()
    {
        Session::forget('cart');
        return back()->with('success', 'Cart cleared successfully.');
    }

    protected function mergeCartAfterLogin($user)
    {
        $sessionCart = Session::get('cart', []);

        if (!empty($sessionCart)) {
            $cart = Cart::firstOrCreate([
                'user_id' => $user->id
            ]);

            foreach ($sessionCart as $productId => $quantity) {
                $product = Product::find($productId);

                if ($product) {
                    $cart->items()->updateOrCreate(
                        ['product_id' => $productId],
                        [
                            'quantity' => $quantity,
                            'unit_price' => $product->price,
                            'subtotal' => $product->price * $quantity
                        ]
                    );
                }
            }

            $cart->update([
                'total_amount' => $cart->items->sum('subtotal')
            ]);

            Session::forget('cart');
        }
    }

    public function addToCart(Request $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'يجب تسجيل الدخول لإضافة المنتجات إلى السلة');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string'
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $cartItem = CartItem::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'size' => $request->size,
                'color' => $request->color
            ],
            [
                'quantity' => $request->quantity,
                'unit_price' => $product->price,
                'subtotal' => $product->price * $request->quantity
            ]
        );

        // تحديث إجمالي السلة
        $cart->update([
            'total_amount' => $cart->items->sum('subtotal')
        ]);

        return back()->with('success', 'تم إضافة المنتج إلى السلة بنجاح');
    }

    public function getItems()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return response()->json([
                'items' => [],
                'total' => 0
            ]);
        }

        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->product->name,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
                'image' => $item->product->images->first()->url ?? null,
            ];
        });

        $total = $items->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        return response()->json([
            'items' => $items,
            'total' => $total
        ]);
    }

    /**
     * تحديث كمية منتج في السلة
     */
    public function updateItem(Request $request, CartItem $cartItem)
    {
        if (!Auth::check() || $cartItem->cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح بهذا الإجراء'
            ], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem->quantity = $request->quantity;
        $cartItem->subtotal = $cartItem->quantity * $cartItem->unit_price;
        $cartItem->save();

        // تحديث إجمالي السلة
        $cart = $cartItem->cart;
        $cart->total_amount = $cart->items->sum('subtotal');
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الكمية بنجاح',
            'item_subtotal' => $cartItem->subtotal,
            'cart_total' => $cart->total_amount,
            'cart_count' => $cart->items->sum('quantity')
        ]);
    }

    /**
     * حذف منتج من السلة
     */
    public function removeItem(CartItem $cartItem)
    {
        if (!Auth::check() || $cartItem->cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح بهذا الإجراء'
            ], 403);
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        // تحديث إجمالي السلة
        $cart->total_amount = $cart->items->sum('subtotal');
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المنتج من السلة بنجاح',
            'cart_total' => $cart->total_amount,
            'cart_count' => $cart->items->sum('quantity')
        ]);
    }
}
