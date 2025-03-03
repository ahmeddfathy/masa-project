<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AppointmentDateTimeUpdated extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        try {
            $sections = [
                [
                    'title' => 'تفاصيل الموعد الجديد',
                    'items' => [
                        "🔖 رقم المرجع: {$this->appointment->reference_number}",
                        "📅 التاريخ الجديد: " . Carbon::parse($this->appointment->appointment_date)->format('Y-m-d'),
                        "⏰ الوقت الجديد: " . Carbon::parse($this->appointment->appointment_time)->format('H:i')
                    ]
                ]
            ];

            if ($this->appointment->notes) {
                $sections[] = [
                    'title' => 'ملاحظات',
                    'items' => [
                        $this->appointment->notes
                    ]
                ];
            }

            return (new MailMessage)
                ->subject("📅 تحديث موعد الزيارة #{$this->appointment->reference_number}")
                ->view('emails.notification', [
                    'title' => '📅 تحديث موعد الزيارة',
                    'name' => $notifiable->name,
                    'greeting' => "مرحباً {$notifiable->name}!",
                    'intro' => 'تم تحديث موعد زيارتك إلى التاريخ والوقت الجديد',
                    'content' => [
                        'sections' => $sections,
                        'action' => [
                            'text' => '👉 تفاصيل الموعد',
                            'url' => route('appointments.show', $this->appointment->reference_number)
                        ],
                        'outro' => [
                            '🙏 شكراً لاختيارك خدماتنا!',
                            '📞 إذا كان لديك أي استفسارات، لا تتردد في الاتصال بنا.'
                        ]
                    ]
                ]);
        } catch (\Throwable $e) {
            Log::error('Error preparing appointment datetime update email', [
                'error' => $e->getMessage(),
                'appointment_reference' => $this->appointment->reference_number
            ]);
            throw $e;
        }
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'تحديث موعد الزيارة',
            'message' => 'تم تحديث موعد زيارتك',
            'type' => 'appointment_datetime_updated',
            'appointment_reference' => $this->appointment->reference_number,
            'appointment_date' => $this->appointment->appointment_date,
            'appointment_time' => $this->appointment->appointment_time
        ];
    }
}
