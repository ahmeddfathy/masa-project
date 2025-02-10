<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\WhatsApp\WhatsAppMessage;
use NotificationChannels\WhatsApp\WhatsAppChannel;

class OrderCreated extends Notification
{
  use Queueable;

  protected $order;

  public function __construct(Order $order)
  {
    $this->order = $order;
  }

  public function via($notifiable): array
  {
    return ['mail', 'database', WhatsAppChannel::class];
  }

  public function toMail($notifiable): MailMessage
  {
    $this->order->load(['items.product', 'items.appointment']);

    $orderItems = $this->order->items->map(function($item) {
      $itemText = $item->quantity . 'x ' . $item->product->name . ' - $' . number_format($item->subtotal, 2);

      if ($item->appointment) {
        $itemText .= "\nموعد المقاسات: " . $item->appointment->appointment_date->format('Y-m-d H:i');
      }

      return $itemText;
    })->join("\n\n");

    return (new MailMessage)
      ->subject('تأكيد الطلب #' . $this->order->id)
      ->greeting('مرحباً ' . $notifiable->name)
      ->line('شكراً لطلبك! تم استلام طلبك بنجاح.')
      ->line('رقم الطلب: ' . $this->order->id)
      ->line('تفاصيل الطلب:')
      ->line($orderItems)
      ->line('معلومات التوصيل:')
      ->line('العنوان: ' . $this->order->shipping_address)
      ->line('رقم الهاتف: ' . $this->order->phone)
      ->line('طريقة الدفع: ' . ($this->order->payment_method === 'card' ? 'بطاقة ائتمان' : 'نقداً عند الاستلام'))
      ->line('إجمالي الطلب: $' . number_format($this->order->total_amount, 2))
      ->action('عرض تفاصيل الطلب', route('orders.show', $this->order))
      ->line('شكراً لتسوقك معنا!')
      ->line('إذا كان لديك أي استفسارات، لا تتردد في الاتصال بنا.');
  }

  /**
   * WhatsApp message
   */
  public function toWhatsApp($notifiable)
  {
    $this->order->load(['items.product']);

    $orderItems = $this->order->items->map(function($item) {
      return "{$item->quantity}x {$item->product->name}";
    })->join("\n");

    $message = "مرحباً {$notifiable->name}!\n\n"
      . "تم استلام طلبك رقم #{$this->order->id} بنجاح.\n\n"
      . "المنتجات:\n{$orderItems}\n\n"
      . "الإجمالي: $" . number_format($this->order->total_amount, 2) . "\n"
      . "العنوان: {$this->order->shipping_address}\n"
      . "رقم الهاتف: {$this->order->phone}\n\n"
      . "شكراً لتسوقك معنا!";

    return WhatsAppMessage::create()
      ->content($message);
  }

  public function toArray($notifiable): array
  {
    return [
      'title' => 'تأكيد الطلب',
      'message' => 'تم استلام طلبك رقم #' . $this->order->id . ' بنجاح',
      'type' => 'order_created',
      'order_id' => $this->order->id,
      'total_amount' => $this->order->total_amount,
      'payment_method' => $this->order->payment_method
    ];
  }
}
