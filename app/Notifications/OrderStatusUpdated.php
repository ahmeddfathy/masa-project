<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;

        // تسجيل بيانات الطلب عند إنشاء الإشعار
        Log::info('Creating order status notification', [
            'order_id' => $order->id,
            'order_status' => $order->order_status,
            'user_id' => $order->user_id,
            'user_email' => $order->user->email ?? 'No email found'
        ]);
    }

    public function via($notifiable): array
    {
        // تسجيل معلومات المستخدم المراد إرسال الإشعار له
        Log::info('Notification channels for user', [
            'user_id' => $notifiable->id,
            'user_email' => $notifiable->email,
            'channels' => ['mail']
        ]);

        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        try {
            // تسجيل محاولة إرسال البريد
            Log::info('Attempting to send email notification', [
                'to_email' => $notifiable->email,
                'user_name' => $notifiable->name,
                'order_id' => $this->order->id
            ]);

            $status = match($this->order->order_status) {
                'pending' => 'قيد الانتظار',
                'processing' => 'قيد المعالجة',
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي',
                default => $this->order->order_status
            };

            Log::info('Preparing order status email', [
                'order_id' => $this->order->id,
                'status' => $status,
                'user' => $notifiable->toArray()
            ]);

            $url = url("/orders/{$this->order->id}");

            return (new MailMessage)
                ->subject("تحديث حالة الطلب: {$status}")
                ->greeting("مرحباً {$notifiable->name}!")
                ->line("تم تحديث حالة طلبك رقم #{$this->order->id} إلى {$status}.")
                ->when($this->order->notes, function ($message) {
                    return $message->line("ملاحظات: {$this->order->notes}");
                })
                ->action('عرض الطلب', $url)
                ->line('شكراً لتسوقك معنا!');
        } catch (\Exception $e) {
            Log::error('Error preparing order status email', [
                'error' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'order_id' => $this->order->id ?? null,
                'user_id' => $notifiable->id ?? null,
                'user_email' => $notifiable->email ?? null
            ]);
            throw $e;
        }
    }

    /**
     * Handle a notification failure.
     */
    public function failed(\Exception $e)
    {
        Log::error('Failed to send order status notification', [
            'error' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'order_id' => $this->order->id ?? null,
            'user_email' => $this->order->user->email ?? null
        ]);
    }
}
