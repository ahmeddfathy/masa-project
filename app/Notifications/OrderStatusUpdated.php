<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        try {
            $statusEmoji = match($this->order->order_status) {
                'pending' => '⏳',
                'processing' => '⚙️',
                'out_for_delivery' => '🚚',
                'on_the_way' => '🛵',
                'delivered' => '✅',
                'completed' => '🎉',
                'returned' => '↩️',
                'cancelled' => '❌',
                default => '📝'
            };

            $status = match($this->order->order_status) {
                'pending' => 'قيد الانتظار',
                'processing' => 'قيد المعالجة',
                'out_for_delivery' => 'جاري التوصيل',
                'on_the_way' => 'في الطريق',
                'delivered' => 'تم التوصيل',
                'completed' => 'مكتمل',
                'returned' => 'مرتجع',
                'cancelled' => 'ملغي',
                default => $this->order->order_status
            };

            $message = match($this->order->order_status) {
                'out_for_delivery' => 'طلبك في طريقه للتوصيل',
                'on_the_way' => 'المندوب في طريقه إليك',
                'delivered' => 'تم توصيل طلبك بنجاح',
                'returned' => 'تم إرجاع طلبك',
                default => "تم تحديث حالة طلبك إلى {$status}"
            };

            $sections = [
                [
                    'title' => 'تفاصيل الطلب',
                    'items' => [
                        "📦 رقم الطلب: #{$this->order->order_number}",
                        "📊 الحالة: {$statusEmoji} {$status}"
                    ]
                ]
            ];

            // إضافة معلومات التوصيل إذا كان الطلب في مرحلة التوصيل
            if (in_array($this->order->order_status, ['out_for_delivery', 'on_the_way'])) {
                $sections[] = [
                    'title' => 'معلومات التوصيل',
                    'items' => [
                        "العنوان: {$this->order->shipping_address}",
                        "رقم الهاتف: {$this->order->phone}"
                    ]
                ];
            }

            // إضافة ملاحظات إذا وجدت
            if ($this->order->notes) {
                $sections[] = [
                    'title' => 'ملاحظات',
                    'items' => [
                        $this->order->notes
                    ]
                ];
            }

            return (new MailMessage)
                ->subject("{$statusEmoji} تحديث حالة الطلب #{$this->order->order_number}")
                ->view('emails.notification', [
                    'title' => "{$statusEmoji} تحديث حالة الطلب",
                    'name' => $notifiable->name,
                    'greeting' => "مرحباً {$notifiable->name}!",
                    'intro' => $message,
                    'content' => [
                        'sections' => $sections,
                        'action' => [
                            'text' => '👉 تفاصيل الطلب',
                            'url' => route('orders.show', $this->order)
                        ],
                        'outro' => [
                            '🙏 شكراً لتسوقك معنا!',
                            '📞 إذا كان لديك أي استفسارات، لا تتردد في الاتصال بنا.'
                        ]
                    ]
                ]);
        } catch (Throwable $e) {
            Log::error('Error preparing order status email', [
                'error' => $e->getMessage(),
                'order_number' => $this->order->order_number
            ]);
            throw $e;
        }
    }

    public function toArray($notifiable): array
    {
        try {
            $status = match($this->order->order_status) {
                'pending' => 'قيد الانتظار',
                'processing' => 'قيد المعالجة',
                'out_for_delivery' => 'جاري التوصيل',
                'on_the_way' => 'في الطريق',
                'delivered' => 'تم التوصيل',
                'completed' => 'مكتمل',
                'returned' => 'مرتجع',
                'cancelled' => 'ملغي',
                default => $this->order->order_status
            };

            $message = match($this->order->order_status) {
                'out_for_delivery' => 'طلبك في طريقه للتوصيل',
                'on_the_way' => 'المندوب في طريقه إليك',
                'delivered' => 'تم توصيل طلبك بنجاح',
                'returned' => 'تم إرجاع طلبك',
                default => "تم تحديث حالة الطلب إلى {$status}"
            };

            return [
                'title' => 'تحديث حالة الطلب',
                'message' => $message,
                'type' => 'order_status_updated',
                'order_number' => $this->order->order_number,
                'status' => $this->order->order_status,
                'status_text' => $status
            ];
        } catch (Throwable $e) {
            Log::error('Error in toArray method', [
                'error' => $e->getMessage(),
                'order_number' => $this->order->order_number
            ]);

            return [
                'title' => 'تحديث حالة الطلب',
                'message' => 'حدث خطأ أثناء معالجة الإشعار',
                'type' => 'order_status_updated',
                'order_number' => $this->order->order_number,
                'status' => $this->order->order_status ?? 'unknown'
            ];
        }
    }

    public function failed(Throwable $e)
    {
        Log::error('Failed to send order status notification', [
            'error' => $e->getMessage(),
            'order_number' => $this->order->order_number ?? null,
            'order_status' => $this->order->order_status ?? null
        ]);
    }
}
