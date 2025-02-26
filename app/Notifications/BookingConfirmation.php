<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Services\FirebaseNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class BookingConfirmation extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;

        try {
            $firebaseService = App::make(FirebaseNotificationService::class);

            if (!$booking->uuid) {
                $booking->uuid = (string) Str::uuid();
                $booking->save();
            }

            if (!$booking->booking_number) {
                $booking->booking_number = 'BN-' . date('y') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
                $booking->save();
            }

            $title = "حجز جديد #{$booking->booking_number}";
            $body = "تم إنشاء حجز جديد للتصوير\n";
            $body .= "العميل: {$booking->user->name}\n";
            $body .= "الخدمة: {$booking->service->name}\n";
            $body .= "الباقة: {$booking->package->name}\n";
            $body .= "التاريخ: " . $booking->session_date->format('Y-m-d') . "\n";
            $body .= "الوقت: " . $booking->session_time->format('H:i') . "\n";
            $body .= "المبلغ: $" . number_format($booking->total_amount, 2);

            if ($booking->baby_name) {
                $body .= "\nاسم الطفل: {$booking->baby_name}";
            }

            $result = $firebaseService->sendNotificationToAdmins(
                $title,
                $body,
                $booking->uuid,
                '/admin/bookings/{uuid}'
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('فشل في إرسال إشعار Firebase:', [
                'booking_id' => $booking->booking_number,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $this->booking->load(['service', 'package', 'addons']);

        $addonsText = $this->booking->addons->map(function($addon) {
            return "• {$addon->name} (الكمية: {$addon->pivot->quantity})";
        })->join("\n");

        return (new MailMessage)
            ->subject('📸 تأكيد الحجز #' . $this->booking->booking_number)
            ->greeting("✨ مرحباً {$notifiable->name}")
            ->line('نشكرك على حجزك! تم تأكيد موعد التصوير بنجاح.')
            ->line('━━━━━━━━━━━━━━━━━━━━━━')
            ->line("📋 رقم الحجز: #{$this->booking->booking_number}")
            ->line('📦 تفاصيل الحجز:')
            ->line("• الخدمة: {$this->booking->service->name}")
            ->line("• الباقة: {$this->booking->package->name}")
            ->line("• التاريخ: " . $this->booking->session_date->format('Y-m-d'))
            ->line("• الوقت: " . $this->booking->session_time->format('H:i'))
            ->when($this->booking->baby_name, function($mail) {
                return $mail->line("• اسم الطفل: {$this->booking->baby_name}");
            })
            ->when($this->booking->baby_birth_date, function($mail) {
                return $mail->line("• تاريخ ميلاد الطفل: " . $this->booking->baby_birth_date->format('Y-m-d'));
            })
            ->when($this->booking->gender, function($mail) {
                return $mail->line("• الجنس: " . ($this->booking->gender === 'male' ? 'ذكر' : 'أنثى'));
            })
            ->when($addonsText, function($mail) use ($addonsText) {
                return $mail->line('📦 الإضافات:')->line($addonsText);
            })
            ->line('━━━━━━━━━━━━━━━━━━━━━━')
            ->line('📍 معلومات التواصل:')
            ->line("الاسم: {$notifiable->name}")
            ->line("رقم الهاتف: {$notifiable->phone}")
            ->line('━━━━━━━━━━━━━━━━━━━━━━')
            ->line('💳 معلومات الدفع:')
            ->line('المبلغ الإجمالي: 💰 $' . number_format($this->booking->total_amount, 2))
            ->line('حالة الدفع: ' . ($this->booking->payment_status === 'paid' ? '✅ مدفوع' : '⏳ قيد الانتظار'))
            ->action('👉 تفاصيل الحجز', route('client.bookings.show', $this->booking->uuid))
            ->line('━━━━━━━━━━━━━━━━━━━━━━')
            ->when($this->booking->notes, function($mail) {
                return $mail->line('📝 ملاحظات:')->line($this->booking->notes);
            })
            ->line('🙏 شكراً لاختيارك خدماتنا!')
            ->line('📞 إذا كان لديك أي استفسارات، لا تتردد في الاتصال بنا.');
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'تأكيد الحجز',
            'message' => 'تم تأكيد حجز التصوير رقم #' . $this->booking->booking_number . ' بنجاح',
            'type' => 'booking_confirmed',
            'booking_id' => $this->booking->id,
            'booking_number' => $this->booking->booking_number,
            'uuid' => $this->booking->uuid,
            'service' => [
                'name' => $this->booking->service->name,
                'id' => $this->booking->service_id
            ],
            'package' => [
                'name' => $this->booking->package->name,
                'id' => $this->booking->package_id
            ],
            'session_date' => $this->booking->session_date->format('Y-m-d'),
            'session_time' => $this->booking->session_time->format('H:i'),
            'total_amount' => $this->booking->total_amount,
            'payment_status' => $this->booking->payment_status
        ];
    }
}
