<?php

namespace App\Services\Customer\Products;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $needs_appointment = $request->needs_appointment;

        if (!$product->is_available) {
            return [
                'success' => false,
                'message' => 'عذراً، هذا المنتج غير متاح حالياً',
                'status' => 422
            ];
        }

        if ($needs_appointment) {
            if (!$product->allow_appointment) {
                return [
                    'success' => false,
                    'message' => 'عذراً، خيار حجز الموعد غير متاح لهذا المنتج',
                    'status' => 422
                ];
            }

            // التحقق من تفعيل ميزة مواعيد المتجر
            if (!\App\Models\Setting::getBool('show_store_appointments', true)) {
                return [
                    'success' => false,
                    'message' => 'عذراً، ميزة مواعيد المتجر غير متاحة حالياً',
                    'status' => 422
                ];
            }
        }

        if (!$needs_appointment) {
            $needsColor = $product->allow_color_selection || $product->allow_custom_color;
            $needsSize = $product->allow_size_selection || $product->allow_custom_size;

            if ($needsColor && empty($request->color)) {
                $colorMessage = '';
                if ($product->allow_color_selection && $product->allow_custom_color) {
                    $colorMessage = 'يرجى اختيار لون أو كتابة اللون المطلوب';
                } else if ($product->allow_color_selection) {
                    $colorMessage = 'يرجى اختيار لون للمنتج';
                } else if ($product->allow_custom_color) {
                    $colorMessage = 'يرجى كتابة اللون المطلوب';
                }

                return [
                    'success' => false,
                    'message' => $colorMessage,
                    'status' => 422
                ];
            }

            if ($needsSize && empty($request->size)) {
                $sizeMessage = '';
                if ($product->allow_size_selection && $product->allow_custom_size) {
                    $sizeMessage = 'يرجى اختيار مقاس أو كتابة المقاس المطلوب';
                } else if ($product->allow_size_selection) {
                    $sizeMessage = 'يرجى اختيار مقاس للمنتج';
                } else if ($product->allow_custom_size) {
                    $sizeMessage = 'يرجى كتابة المقاس المطلوب';
                }

                return [
                    'success' => false,
                    'message' => $sizeMessage,
                    'status' => 422
                ];
            }
        }

        $cart = $this->getOrCreateCart($request);
        $cartItem = $this->findOrCreateCartItem($cart, $product, $request);

        return [
            'success' => true,
            'message' => 'تمت إضافة المنتج إلى سلة التسوق',
            'cart_count' => $cart->items()->sum('quantity'),
            'cart_total' => $cart->total_amount,
            'show_appointment' => $needs_appointment,
            'product_name' => $product->name,
            'product_id' => $product->id,
            'cart_item_id' => $cartItem->id,
            'show_modal' => $needs_appointment,
            'status' => 200
        ];
    }

    public function getOrCreateCart(Request $request)
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => Str::random(40)]
            );
        } else {
            $sessionId = $request->session()->get('cart_session_id');
            if (!$sessionId) {
                $sessionId = Str::random(40);
                $request->session()->put('cart_session_id', $sessionId);
            }
            return Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['total_amount' => 0]
            );
        }
    }

    public function findOrCreateCartItem($cart, $product, $request)
    {
        // البحث عن العنصر في السلة مع مراعاة خيار الكمية
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('quantity_option_id', $request->quantity_option_id)
            ->where('needs_appointment', $request->needs_appointment)
            ->where(function($query) use ($request) {
                $query->where('color', $request->color)->orWhereNull('color');
            })
            ->where(function($query) use ($request) {
                $query->where('size', $request->size)->orWhereNull('size');
            })
            ->first();

        // تحديد السعر بناءً على خيار الكمية أو المقاس
        $itemPrice = 0; // Default price is 0 if no price source is found
        $sizePrice = 0;
        $quantityPrice = 0;

        // جمع سعر الكمية إذا تم اختيارها
        if ($request->quantity_option_id) {
            $quantityOption = $product->quantities()->find($request->quantity_option_id);
            if ($quantityOption) {
                $quantityPrice = $quantityOption->price;
            }
        }

        // جمع سعر المقاس إذا تم اختياره
        if ($request->size && $product->enable_size_selection) {
            $size = $product->sizes->where('size', $request->size)->first();
            if ($size && $size->price) {
                $sizePrice = $size->price;
            }
        }

        // إذا تم اختيار كلاهما، نجمع السعرين معًا
        if ($quantityPrice > 0 && $sizePrice > 0) {
            $itemPrice = $quantityPrice + $sizePrice;
        }
        // إذا تم اختيار واحد فقط، نستخدم سعره
        elseif ($quantityPrice > 0) {
            $itemPrice = $quantityPrice;
        }
        elseif ($sizePrice > 0) {
            $itemPrice = $sizePrice;
        }
        // إذا لم يتم اختيار أي منهما، نستخدم أقل سعر متاح
        else {
            $priceRange = $product->getPriceRange();
            $itemPrice = $priceRange['min'];
        }

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->unit_price = $itemPrice;
            $cartItem->subtotal = $cartItem->quantity * $itemPrice;
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'unit_price' => $itemPrice,
                'subtotal' => $request->quantity * $itemPrice,
                'color' => $request->color,
                'size' => $request->size,
                'quantity_option_id' => $request->quantity_option_id,
                'needs_appointment' => $request->needs_appointment
            ]);
        }

        $cart->total_amount = $cart->items()->sum('subtotal');
        $cart->save();

        return $cartItem;
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
            return [
                'items' => [],
                'total' => 0,
                'count' => 0
            ];
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
                'color' => $item->color,
                'size' => $item->size,
                'needs_appointment' => $item->needs_appointment,
                'has_appointment' => $item->appointment()->exists()
            ];
        });

        return [
            'items' => $items,
            'total' => $cart->total_amount,
            'count' => $cart->items()->sum('quantity')
        ];
    }

    public function updateCartItem(CartItem $cartItem, $quantity)
    {
        $cartItem->quantity = $quantity;
        $cartItem->subtotal = $cartItem->quantity * $cartItem->unit_price;
        $cartItem->save();

        $cart = $cartItem->cart;
        $cart->total_amount = $cart->items()->sum('subtotal');
        $cart->save();

        return [
            'success' => true,
            'message' => 'تم تحديث الكمية بنجاح',
            'item_subtotal' => $cartItem->subtotal,
            'cart_total' => $cart->total_amount,
            'cart_count' => $cart->items()->sum('quantity')
        ];
    }

    public function removeCartItem(CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== Auth::id()) {
            return [
                'success' => false,
                'message' => 'غير مصرح بهذا الإجراء',
                'status' => 403
            ];
        }

        // البحث عن الموعد المرتبط بعنصر السلة
        $appointment = Appointment::where('cart_item_id', $cartItem->id)->first();

        if ($appointment) {
            // حذف الموعد نهائياً
            $appointment->forceDelete();
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        $cart->updateTotals();

        return [
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
        ];
    }
}
