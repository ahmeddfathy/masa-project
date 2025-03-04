<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Appointment;

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
                'total' => 0,
                'cart_items_count' => 0
            ]);
        }

        $cart_items = $cart->items()
            ->with(['product.images', 'product.category', 'appointment', 'product.sizes', 'product.quantities'])
            ->get();

        $subtotal = $cart_items->sum('subtotal');
        $total = $subtotal;

        return view('cart.index', [
            'cart_items' => $cart_items,
            'subtotal' => $subtotal,
            'total' => $total,
            'cart_items_count' => $cart_items->sum('quantity')
        ]);
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

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول لإضافة المنتجات إلى السلة'
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'quantity_option_id' => 'nullable|exists:product_quantity_options,id',
            'needs_appointment' => 'required|boolean'
        ]);

        try {
            $product = Product::findOrFail($request->product_id);

            if (!$product->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنتج غير متوفر حالياً'
                ], 400);
            }

            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);

            // حساب السعر النهائي
            $finalPrice = $this->calculateFinalPrice($product, $request);

            // البحث عن عنصر مشابه في السلة
            $cartItem = $cart->items()
                ->where('product_id', $product->id)
                ->where('size', $request->size)
                ->where('color', $request->color)
                ->where('quantity_option_id', $request->quantity_option_id)
                ->first();

            if ($cartItem) {
                // تحديث الكمية إذا كان المنتج موجود
                $cartItem->quantity += $request->quantity;
                $cartItem->subtotal = $finalPrice * $cartItem->quantity;
                $cartItem->save();
            } else {
                // إنشاء عنصر جديد
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'size' => $request->size,
                    'color' => $request->color,
                    'unit_price' => $finalPrice,
                    'subtotal' => $finalPrice * $request->quantity,
                    'quantity_option_id' => $request->quantity_option_id,
                    'needs_appointment' => $request->needs_appointment
                ]);
            }

            // تحديث إجمالي السلة
            $this->updateCartTotal($cart);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المنتج إلى السلة بنجاح',
                'cart_count' => $cart->items->sum('quantity'),
                'cart_total' => number_format($cart->total_amount, 2),
                'show_appointment' => $request->needs_appointment,
                'product_name' => $product->name,
                'product_id' => $product->id,
                'cart_item_id' => $cartItem->id,
                'show_modal' => $request->needs_appointment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة المنتج إلى السلة'
            ], 500);
        }
    }

    protected function calculateFinalPrice($product, $request)
    {
        $basePrice = $product->price;

        // إذا كان هناك خيار كمية محدد
        if ($request->quantity_option_id) {
            $quantityOption = $product->quantities()
                ->where('id', $request->quantity_option_id)
                ->first();

            if ($quantityOption) {
                return $quantityOption->price;
            }
        }

        // إذا كان هناك مقاس محدد
        if ($request->size) {
            $sizeOption = $product->sizes()
                ->where('size', $request->size)
                ->first();

            if ($sizeOption && $sizeOption->price) {
                return $sizeOption->price;
            }
        }

        return $basePrice;
    }

    protected function updateCartTotal($cart)
    {
        $cart->refresh();
        $total = $cart->items->sum('subtotal');
        $cart->update(['total_amount' => $total]);
    }

    public function getItems()
    {
        $cart = Cart::where('user_id', Auth::id())->first();

        if (!$cart) {
            return response()->json([
                'items' => [],
                'total' => '0.00',
                'count' => 0
            ]);
        }

        $items = $cart->items->map(function ($item) {
            $product = $item->product;
            $image = $product->images->first() ?
                     asset('storage/' . $product->images->first()->image_path) :
                     null;

            return [
                'id' => $item->id,
                'name' => $product->name,
                'price' => number_format($item->unit_price, 2),
                'quantity' => $item->quantity,
                'subtotal' => number_format($item->subtotal, 2),
                'image' => $image,
                'color' => $item->color,
                'size' => $item->size,
                'needs_appointment' => $item->needs_appointment,
                'has_appointment' => $item->appointment()->exists()
            ];
        });

        return response()->json([
            'items' => $items,
            'total' => number_format($cart->total_amount, 2),
            'count' => $cart->items->sum('quantity')
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

        try {
            $cartItem->quantity = $request->quantity;
            $cartItem->subtotal = $cartItem->unit_price * $cartItem->quantity;
            $cartItem->save();

            $cart = $cartItem->cart;
            $this->updateCartTotal($cart);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الكمية بنجاح',
                'item_subtotal' => $cartItem->subtotal,
                'cart_total' => $cart->total_amount,
                'cart_count' => $cart->items->sum('quantity')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الكمية'
            ], 500);
        }
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

        try {
            $cart = $cartItem->cart;

            // حذف الموعد إذا كان موجود
            if ($appointment = $cartItem->appointment) {
                $appointment->delete();
            }

            $cartItem->delete();
            $this->updateCartTotal($cart);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج من السلة بنجاح',
                'cart_total' => $cart->total_amount,
                'cart_count' => $cart->items->sum('quantity')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنتج'
            ], 500);
        }
    }

    public function checkAppointment(CartItem $cartItem)
    {
        // التحقق من ملكية العنصر
        if ($cartItem->cart->user_id !== Auth::id()) {
            return response()->json([
                'needs_appointment' => false,
                'message' => 'غير مصرح بهذا الإجراء'
            ], 403);
        }

        return response()->json([
            'needs_appointment' => $cartItem->needsAppointment(),
            'has_appointment' => $cartItem->appointment()->exists()
        ]);
    }
}
