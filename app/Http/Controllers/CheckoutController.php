<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CheckoutException;
use App\Models\Appointment;
use App\Models\CartItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Notifications\OrderCreated;

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

    // تحقق من المنتجات التي تحتاج إلى موعد ولم يتم حجز موعد لها بعد
    $itemNeedsAppointment = $cart->items
      ->filter(function ($item) {
        // البحث عن الموعد باستخدام cart_item_id
        $appointment = Appointment::where('cart_item_id', $item->id)->first();

        Log::info('Checking item appointment status', [
          'cart_item_id' => $item->id,
          'product_id' => $item->product_id,
          'needs_appointment' => $item->product->needs_appointment,
          'has_appointment' => !is_null($appointment)
        ]);

        return $item->product->needs_appointment && is_null($appointment);
      })
      ->first();

    if ($itemNeedsAppointment) {
      Log::warning('Item needs appointment', [
        'cart_item_id' => $itemNeedsAppointment->id,
        'product_id' => $itemNeedsAppointment->product_id
      ]);
      return redirect()->route('appointments.create', [
        'cart_item_id' => $itemNeedsAppointment->id
      ])->with('info', 'يجب حجز موعد للمقاسات أولاً');
    }

    return view('checkout.index', compact('cart'));
  }

  public function store(Request $request)
  {
    Log::info('Checkout process started', [
      'user_id' => Auth::id(),
      'request_data' => $request->all()
    ]);

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

      // التحقق من المواعيد المطلوبة
      $itemNeedsAppointment = $cart->items
        ->filter(function ($item) {
          // البحث عن الموعد باستخدام cart_item_id
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
        'notes' => ['nullable', 'string', 'max:1000']
      ]);

      return DB::transaction(function () use ($request, $validated, $cart) {
        // التحقق من المخزون
        foreach ($cart->items as $item) {
          if ($item->product->stock < $item->quantity) {
            Log::warning('Insufficient stock', [
              'product_id' => $item->product_id,
              'requested_quantity' => $item->quantity,
              'available_stock' => $item->product->stock
            ]);
            throw new CheckoutException("الكمية المطلوبة غير متوفرة من {$item->product->name}");
          }
        }

        $totalAmount = $cart->total_amount;

        // إنشاء الطلب
        $order = Order::create([
          'user_id' => Auth::id(),
          'total_amount' => $totalAmount,
          'shipping_address' => $validated['shipping_address'],
          'phone' => $validated['phone'],
          'payment_method' => 'cash',
          'payment_status' => Order::PAYMENT_STATUS_PENDING,
          'order_status' => Order::ORDER_STATUS_PENDING,
          'notes' => $validated['notes'] ?? null
        ]);

        Log::info('Order created', [
          'order_id' => $order->id
        ]);

        // إضافة منتجات الطلب
        foreach ($cart->items as $item) {
          // البحث عن الموعد المرتبط بعنصر السلة
          $appointment = Appointment::where('cart_item_id', $item->id)->first();

          // إنشاء عنصر الطلب
          $orderItem = $order->items()->create([
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'unit_price' => $item->product->price,
            'subtotal' => $item->product->price * $item->quantity,
            'appointment_id' => $appointment ? $appointment->id : null,
            'color' => $item->color,
            'size' => $item->size
          ]);

          // تحديث حالة الموعد إذا وجد
          if ($appointment) {
            $appointment->update([
              'status' => Appointment::STATUS_APPROVED,
              'order_item_id' => $orderItem->id
            ]);

            Log::info('Appointment status updated', [
              'appointment_id' => $appointment->id,
              'status' => Appointment::STATUS_APPROVED,
              'order_item_id' => $orderItem->id
            ]);
          }
        }

        // حذف السلة
        $cart->items()->delete();
        $cart->delete();

        try {
          // إرسال إشعار تأكيد الطلب مع التقاط أي أخطاء
          Auth::user()->notify(new OrderCreated($order));
        } catch (\Exception $e) {
          Log::error('Failed to send order notification', [
            'order_id' => $order->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
          ]);
          // نستمر في العملية حتى لو فشل إرسال الإشعار
        }

        Log::info('Checkout completed successfully', [
          'order_id' => $order->id
        ]);

        return redirect()->route('orders.show', $order)
          ->with('success', 'تم إنشاء الطلب بنجاح');
      });
    } catch (ValidationException $e) {
      Log::error('Validation failed', [
        'errors' => $e->errors(),
        'user_id' => Auth::id()
      ]);
      return back()->withErrors($e->errors())->withInput();
    } catch (CheckoutException $e) {
      Log::error('Checkout failed', [
        'error' => $e->getMessage(),
        'user_id' => Auth::id()
      ]);
      return back()
        ->withInput()
        ->withErrors(['error' => $e->getMessage()]);
    } catch (\Exception $e) {
      Log::error('Checkout error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return back()
        ->withInput()
        ->withErrors(['error' => 'حدث خطأ غير متوقع. الرجاء المحاولة مرة أخرى أو الاتصال بالدعم الفني.']);
    }
  }

  /**
   * معالجة عملية الدفع
   *
   * @param Order $order
   * @return object
   */
  protected function processPayment(Order $order)
  {
    Log::info('Processing payment', [
      'order_id' => $order->id,
      'amount' => $order->total_amount
    ]);

    // هنا يتم إضافة منطق معالجة الدفع
    return (object) ['success' => true];
  }
}
