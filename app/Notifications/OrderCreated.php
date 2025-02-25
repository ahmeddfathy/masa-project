<?php

namespace App\Notifications;

use App\Models\Order;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;

class OrderCreated extends Notification
{
  use Queueable;

  protected $order;

  public function __construct(Order $order)
  {
    $this->order = $order;

    try {
      $firebaseService = App::make(FirebaseNotificationService::class);

      $title = "طلب جديد #{$order->order_number}";
      $body = "تم إنشاء طلب جديد بقيمة $" . number_format($order->total_amount, 2);

      $itemsWithAppointments = $order->items->filter(function($item) {
        return $item->appointment !== null;
      });

      if ($itemsWithAppointments->isNotEmpty()) {
        $body .= "\n\nمواعيد المقاسات:";
        foreach ($itemsWithAppointments as $item) {
          $body .= "\nالمنتج: {$item->product->name}";
          $body .= "\nالموعد: " . $item->appointment->appointment_date->format('Y-m-d H:i');
          $body .= "\nرقم المرجع: " . $item->appointment->reference_number;
        }
      }

      $result = $firebaseService->sendNotificationToAdmins(
        $title,
        $body,
        $order->uuid
      );
    } catch (\Exception $e) {
    }
  }

  public function via($notifiable): array
  {
    return ['mail', 'database'];
  }

  public function toMail($notifiable): MailMessage
  {
    $this->order->load(['items.product', 'items.appointment']);

    $orderItems = $this->order->items->map(function($item) {
        $itemText = "• {$item->product->name}\n";
        $itemText .= "  الكمية: {$item->quantity}\n";
        $itemText .= "  السعر: $" . number_format($item->subtotal, 2);

        if ($item->appointment) {
            $itemText .= "\n  موعد المقاسات: " . $item->appointment->appointment_date->format('Y-m-d H:i');
            $itemText .= "\n  رقم المرجع: " . $item->appointment->reference_number;
        }

        return $itemText;
    })->join("\n\n");

    return (new MailMessage)
        ->subject('🛍️ تأكيد الطلب #' . $this->order->order_number)
        ->greeting("✨ مرحباً {$notifiable->name}")
        ->line('نشكرك على ثقتك! تم استلام طلبك بنجاح.')
        ->line('━━━━━━━━━━━━━━━━━━━━━━')
        ->line("📦 رقم الطلب: #{$this->order->order_number}")
        ->line('🛒 تفاصيل المنتجات:')
        ->line($orderItems)
        ->line('━━━━━━━━━━━━━━━━━━━━━━')
        ->line('📍 معلومات التوصيل:')
        ->line("العنوان: {$this->order->shipping_address}")
        ->line("رقم الهاتف: {$this->order->phone}")
        ->line('━━━━━━━━━━━━━━━━━━━━━━')
        ->line('💳 معلومات الدفع:')
        ->line('طريقة الدفع: ' . ($this->order->payment_method === 'card' ? '💳 بطاقة ائتمان' : '💵 نقداً عند الاستلام'))
        ->line('إجمالي الطلب: 💰 $' . number_format($this->order->total_amount, 2))
        ->action('👉 متابعة الطلب', route('orders.show', $this->order))
        ->line('━━━━━━━━━━━━━━━━━━━━━━')
        ->line('🙏 شكراً لتسوقك معنا!')
        ->line('📞 إذا كان لديك أي استفسارات، لا تتردد في الاتصال بنا.');
  }

  public function toArray($notifiable): array
  {
    $data = [
      'title' => 'تأكيد الطلب',
      'message' => 'تم استلام طلبك رقم #' . $this->order->order_number . ' بنجاح',
      'type' => 'order_created',
      'order_number' => $this->order->order_number,
      'total_amount' => $this->order->total_amount,
      'payment_method' => $this->order->payment_method
    ];

    $appointments = $this->order->items
      ->filter(function($item) {
        return $item->appointment !== null;
      })
      ->map(function($item) {
        return [
          'product_name' => $item->product->name,
          'date' => $item->appointment->appointment_date->format('Y-m-d H:i'),
          'reference_number' => $item->appointment->reference_number
        ];
      });

    if ($appointments->isNotEmpty()) {
      $data['appointments'] = $appointments->values()->all();
    }

    return $data;
  }
}
