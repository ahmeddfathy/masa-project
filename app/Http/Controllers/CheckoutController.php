<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CheckoutException;
use App\Models\Appointment;
use App\Notifications\OrderCreated;
use App\Services\Store\StorePaymentService;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
  protected $paymentService;

  public function __construct(StorePaymentService $paymentService)
  {
    $this->paymentService = $paymentService;
  }

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
        'payment_method' => ['required', 'in:cash,online'],
        'policy_agreement' => ['required', 'accepted']
      ]);

      return DB::transaction(function () use ($request, $validated, $cart) {
        foreach ($cart->items as $item) {
          if ($item->product->stock < $item->quantity) {
            throw new CheckoutException("الكمية المطلوبة غير متوفرة من {$item->product->name}");
          }
        }

        $totalAmount = $cart->total_amount;

        // If payment method is cash, create order directly
        if ($validated['payment_method'] === 'cash') {
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
                'status' => Appointment::STATUS_PENDING,
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
        }

        // For online payment, prepare order data and redirect to payment gateway
        $paymentId = 'ORDER-' . strtoupper(Str::random(8)) . '-' . time();

        // Prepare order data for payment
        $orderItems = [];
        foreach ($cart->items as $item) {
            $appointment = Appointment::where('cart_item_id', $item->id)->first();
            $orderItems[] = [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
                'appointment_id' => $appointment ? $appointment->id : null,
                'color' => $item->color,
                'size' => $item->size
            ];
        }

        $orderData = [
            'user_id' => Auth::id(),
            'total_amount' => $totalAmount,
            'shipping_address' => $validated['shipping_address'],
            'phone' => $validated['phone'],
            'payment_method' => 'online',
            'notes' => $validated['notes'] ?? null,
            'payment_id' => $paymentId,
            'items' => $orderItems
        ];

        // Store order data in session
        session(['pending_order' => $orderData]);

        // Initiate payment
        $paymentResult = $this->paymentService->initiatePayment($orderData, $totalAmount, Auth::user());

        if ($paymentResult['success'] && !empty($paymentResult['redirect_url'])) {
            // Store transaction ID in session
            session(['payment_transaction_id' => $paymentResult['transaction_id']]);

            // Clear cart before redirecting to payment gateway
            $cart->items()->delete();
            $cart->delete();

            return redirect($paymentResult['redirect_url']);
        }

        // If payment initialization failed
        throw new CheckoutException('فشل الاتصال ببوابة الدفع: ' . ($paymentResult['message'] ?? 'خطأ غير معروف'));
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

  /**
   * Handle payment callback from PayTabs
   */
  public function paymentCallback(Request $request)
  {
    try {
        // Process payment response
        $paymentData = $this->paymentService->processPaymentResponse($request);

        // Check for pending order data
        $orderData = session('pending_order');
        if (!$orderData) {
            return redirect()->route('checkout.index')
                ->with('error', 'خطأ في الدفع - لم يتم العثور على بيانات الطلب');
        }

        // Look for existing order
        $existingOrder = $this->paymentService->findExistingOrder($paymentData);

        if ($existingOrder) {
            // Update existing order payment status
            $this->paymentService->updateOrderPaymentStatus($existingOrder, $paymentData);

            session()->forget(['pending_order', 'payment_transaction_id']);

            return redirect()->route('orders.show', $existingOrder)
                ->with('success', 'تم تأكيد الدفع بنجاح!');
        }

        // If payment failed
        if (!$paymentData['isSuccessful'] && !$paymentData['isPending']) {
            session()->forget(['pending_order', 'payment_transaction_id']);

            return redirect()->route('checkout.index')
                ->with('error', 'فشل الدفع: ' . ($paymentData['message'] ?: 'خطأ غير معروف'));
        }

        // Create new order
        $order = $this->paymentService->createOrderFromPayment($orderData, $paymentData);

        // Clear session data
        session()->forget(['pending_order', 'payment_transaction_id']);

        // Send order confirmation notification
        $order->user->notify(new OrderCreated($order));

        $message = $paymentData['isSuccessful']
            ? 'تم تأكيد الدفع وإنشاء الطلب بنجاح!'
            : 'تم إنشاء الطلب، جارٍ التحقق من حالة الدفع...';

        return redirect()->route('orders.show', $order)
            ->with('success', $message);

    } catch (\Exception $e) {
        Log::error('Error processing payment callback: ' . $e->getMessage(), [
            'exception' => $e,
            'request_data' => $request->all()
        ]);

        return redirect()->route('checkout.index')
            ->with('error', 'حدث خطأ أثناء معالجة الدفع. الرجاء الاتصال بالدعم الفني.');
    }
  }

  /**
   * Handle user return from payment gateway
   */
  public function paymentReturn(Request $request)
  {
    return $this->paymentCallback($request);
  }
}
