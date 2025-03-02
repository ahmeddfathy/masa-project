<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CheckoutException;
use App\Models\Appointment;
use App\Notifications\OrderCreated;

use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
  public function index()
  {
    if (!Auth::check()) {
      return redirect()->route('login')
        ->with('error', 'يجب تسجيل الدخول لإتمام عملية الشراء');
    }

    $cart = Cart::with(['items.product', 'items.appointment'])
      ->where('user_id', Auth::id())
      ->first();

    if (!$cart || $cart->items->isEmpty()) {
      return redirect()->route('products.index')
        ->with('error', 'السلة فارغة');
    }

    $itemNeedsAppointment = $cart->items
      ->filter(function ($item) {
        $appointment = Appointment::where('cart_item_id', $item->id)->first();
        return $item->product->needs_appointment && is_null($appointment);
      })
      ->first();

    if ($itemNeedsAppointment) {
      return redirect()->route('appointments.create', [
        'cart_item_id' => $itemNeedsAppointment->id
      ])->with('info', 'يجب حجز موعد للمقاسات أولاً');
    }

    return view('checkout.index', compact('cart'));
  }

  public function store(Request $request)
  {
    try {
      if (!Auth::check()) {
        throw new CheckoutException('يجب تسجيل الدخول لإتمام عملية الشراء');
      }

      $cart = Cart::where('user_id', Auth::id())
        ->with(['items.product'])
        ->first();

      if (!$cart || $cart->items->isEmpty()) {
        throw new CheckoutException('السلة فارغة');
      }

      $itemNeedsAppointment = $cart->items
        ->filter(function ($item) {
          $appointment = Appointment::where('cart_item_id', $item->id)->first();
          return $item->product->needs_appointment && is_null($appointment);
        })
        ->first();

      if ($itemNeedsAppointment) {
        throw new CheckoutException('يجب حجز موعد للمقاسات أولاً');
      }

      $validated = $request->validate([
        'shipping_address' => ['required', 'string', 'max:500'],
        'phone' => ['required', 'string', 'max:20', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'policy_agreement' => ['required', 'accepted']
      ]);

      return DB::transaction(function () use ($request, $validated, $cart) {
        foreach ($cart->items as $item) {
          if ($item->product->stock < $item->quantity) {
            throw new CheckoutException("الكمية المطلوبة غير متوفرة من {$item->product->name}");
          }
        }

        $totalAmount = $cart->total_amount;

        $orderData = [
          'user_id' => Auth::id(),
          'total_amount' => $totalAmount,
          'shipping_address' => $validated['shipping_address'],
          'phone' => $validated['phone'],
          'payment_method' => 'cash',
          'payment_status' => Order::PAYMENT_STATUS_PENDING,
          'order_status' => Order::ORDER_STATUS_PENDING,
          'notes' => $validated['notes'] ?? null,
          'policy_agreement' => true,
          'amount_paid' => 0
        ];

        $order = Order::create($orderData);

        foreach ($cart->items as $item) {
          $appointment = Appointment::where('cart_item_id', $item->id)->first();

          $orderItem = $order->items()->create([
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->unit_price,
            'subtotal' => $item->subtotal,
            'appointment_id' => $appointment ? $appointment->id : null,
            'color' => $item->color,
            'size' => $item->size
          ]);

          if ($appointment) {
            $appointment->update([
              'status' => Appointment::STATUS_PENDING, // Changed from STATUS_APPROVED to STATUS_PENDING
              'order_item_id' => $orderItem->id
            ]);
          }
        }

        $cart->items()->delete();
        $cart->delete();

        // Send order confirmation notification
        $order->user->notify(new OrderCreated($order));

        return redirect()->route('orders.show', $order)
          ->with('success', 'تم إنشاء الطلب بنجاح');
      });
    } catch (ValidationException $e) {
      return back()->withErrors($e->errors())->withInput();
    } catch (CheckoutException $e) {
      return back()
        ->withInput()
        ->withErrors(['error' => $e->getMessage()]);
    } catch (\Exception $e) {
      return back()
        ->withInput()
        ->withErrors(['error' => 'حدث خطأ غير متوقع. الرجاء المحاولة مرة أخرى أو الاتصال بالدعم الفني.']);
    }
  }

  protected function processPayment(Order $order)
  {
    return (object) ['success' => true];
  }
}
