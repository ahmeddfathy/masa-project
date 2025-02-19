<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class AppointmentStatusUpdated extends Notification
{
    use Queueable, SerializesModels;

    protected $appointment;
    private $appointmentId;
    private $appointmentStatus;
    private $appointmentDate;
    private $appointmentTime;
    private $appointmentNotes;
    private $userId;

    public function __construct(Appointment $appointment)
    {
        try {
            $this->appointment = $appointment;

            if (!$appointment->exists) {
                throw new \Exception('Appointment model does not exist');
            }

            // Store essential data as primitive types
            $this->appointmentId = $appointment->id;
            $this->appointmentStatus = $appointment->status;
            $this->appointmentDate = $appointment->date instanceof Carbon ? $appointment->date->format('Y-m-d') : 'غير محدد';
            $this->appointmentTime = $appointment->time ?? 'غير محدد';
            $this->appointmentNotes = $appointment->notes;
            $this->userId = $appointment->user_id;

            Log::info('Creating appointment status notification', [
                'appointment_id' => $this->appointmentId,
                'status' => $this->appointmentStatus,
                'date' => $this->appointmentDate,
                'time' => $this->appointmentTime,
                'user_id' => $this->userId,
                'user_email' => $appointment->user->email ?? 'No email found'
            ]);
        } catch (Throwable $e) {
            Log::error('Error in AppointmentStatusUpdated constructor', [
                'error' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'appointment_id' => $appointment->id ?? null
            ]);
            throw $e;
        }
    }

    public function via($notifiable): array
    {
        try {
            $channels = ['database'];

            if ($notifiable && $notifiable->email) {
                $channels[] = 'mail';
            }

            Log::info('Notification channels for user', [
                'user_id' => $notifiable->id ?? null,
                'user_email' => $notifiable->email ?? null,
                'channels' => $channels
            ]);

            return $channels;
        } catch (Throwable $e) {
            Log::error('Error in via method', [
                'error' => $e->getMessage(),
                'notifiable_id' => $notifiable->id ?? null
            ]);
            return ['database']; // Fallback to database only
        }
    }

    public function toMail($notifiable): MailMessage
    {
        try {
            $statusEmoji = match($this->appointmentStatus) {
                'pending' => '⏳',
                'confirmed' => '✅',
                'cancelled' => '❌',
                'completed' => '🎉',
                'approved' => '👍',
                default => '📝'
            };

            $status = match($this->appointmentStatus) {
                'pending' => 'قيد الانتظار',
                'confirmed' => 'مؤكد',
                'cancelled' => 'ملغي',
                'completed' => 'مكتمل',
                'approved' => 'موافق عليه',
                default => ucfirst($this->appointmentStatus)
            };

            $message = (new MailMessage)
                ->subject("{$statusEmoji} تحديث حالة الموعد - {$this->appointment->reference_number}")
                ->greeting("✨ مرحباً {$notifiable->name}!")
                ->line("تم تحديث حالة موعدك إلى: {$statusEmoji} {$status}")
                ->line('━━━━━━━━━━━━━━━━━━━━━━')
                ->line("🔖 رقم المرجع: {$this->appointment->reference_number}");

            if ($this->appointmentDate !== 'غير محدد') {
                $message->line("📅 التاريخ: {$this->appointmentDate}");
            }
            if ($this->appointmentTime !== 'غير محدد') {
                $message->line("⏰ الوقت: {$this->appointmentTime}");
            }

            if ($this->appointmentNotes) {
                $message->line('━━━━━━━━━━━━━━━━━━━━━━')
                       ->line("📝 ملاحظات: {$this->appointmentNotes}");
            }

            return $message
                ->line('━━━━━━━━━━━━━━━━━━━━━━')
                ->action('👉 تفاصيل الموعد', route('appointments.show', $this->appointment->reference_number))
                ->line('🙏 شكراً لاختيارك خدماتنا!')
                ->line('📞 إذا كان لديك أي استفسارات، لا تتردد في الاتصال بنا.');
        } catch (Throwable $e) {
            Log::error('Error preparing appointment status email', [
                'error' => $e->getMessage(),
                'appointment_reference' => $this->appointment->reference_number
            ]);
            throw $e;
        }
    }

    public function toArray($notifiable): array
    {
        try {
            $status = match($this->appointmentStatus) {
                'pending' => 'قيد الانتظار',
                'confirmed' => 'مؤكد',
                'cancelled' => 'ملغي',
                'completed' => 'مكتمل',
                'approved' => 'موافق عليه',
                default => ucfirst($this->appointmentStatus)
            };

            $message = "تم تحديث حالة موعدك إلى {$status}";
            if ($this->appointmentDate !== 'غير محدد' && $this->appointmentTime !== 'غير محدد') {
                $message .= " (التاريخ: {$this->appointmentDate} الساعة {$this->appointmentTime})";
            }

            return [
                'title' => 'تحديث حالة الموعد',
                'message' => $message,
                'type' => 'appointment_status_updated',
                'reference_number' => $this->appointment->reference_number,
                'status' => $this->appointmentStatus
            ];
        } catch (Throwable $e) {
            Log::error('Error in toArray method', [
                'error' => $e->getMessage(),
                'appointment_id' => $this->appointmentId
            ]);

            return [
                'title' => 'تحديث حالة الموعد',
                'message' => 'حدث خطأ أثناء معالجة الإشعار',
                'type' => 'appointment_status_updated',
                'reference_number' => $this->appointment->reference_number,
                'status' => $this->appointmentStatus ?? 'unknown'
            ];
        }
    }

    public function failed(Throwable $e)
    {
        Log::error('Failed to send appointment status notification', [
            'error' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'appointment_id' => $this->appointmentId ?? null,
            'appointment_data' => [
                'status' => $this->appointmentStatus ?? null,
                'date' => $this->appointmentDate ?? null,
                'time' => $this->appointmentTime ?? null
            ]
        ]);
    }
}
