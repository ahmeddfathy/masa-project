<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Notifications\OrderCreated;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()
            ->with(['items.product'])
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items.product']);
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $products = Product::whereIn('id', array_keys($cart))->get();
            $total = 0;

            // Check stock and calculate total
            foreach ($products as $product) {
                $quantity = $cart[$product->id];
                if ($product->stock < $quantity) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }
                $total += $product->price * $quantity;
            }

            // Create order - UUID and order_number will be auto-generated
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'shipping_address' => $validated['shipping_address'],
                'phone' => $validated['phone'],
                'notes' => $validated['notes'],
                'status' => Order::STATUS_PENDING
            ]);

            // Create order items and update stock
            foreach ($products as $product) {
                $quantity = $cart[$product->id];
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $product->price * $quantity
                ]);
            }

            DB::commit();

            // Clear cart after successful order
            Session::forget('cart');

            // Send order confirmation notification
            Auth::user()->notify(new OrderCreated($order));

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
