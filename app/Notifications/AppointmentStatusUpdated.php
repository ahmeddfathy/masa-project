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
    protected $isDateTimeUpdate;

    public function __construct(Appointment $appointment, bool $isDateTimeUpdate = false)
    {
        try {
            $this->appointment = $appointment;
            $this->isDateTimeUpdate = $isDateTimeUpdate;

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

            $sections = [
                [
                    'title' => 'تفاصيل الموعد',
                    'items' => [
                        "🔖 رقم المرجع: {$this->appointment->reference_number}",
                        "📅 التاريخ: " . Carbon::parse($this->appointment->date)->format('Y-m-d'),
                        "⏰ الوقت: " . Carbon::parse($this->appointment->time)->format('H:i'),
                        "📊 الحالة الجديدة: {$statusEmoji} {$status}"
                    ]
                ]
            ];

            if ($this->appointmentNotes) {
                $sections[] = [
                    'title' => 'ملاحظات',
                    'items' => [
                        $this->appointmentNotes
                    ]
                ];
            }

            // إضافة معلومات الدفع إذا تم تأكيد الموعد
            if ($this->appointmentStatus === 'approved') {
                $sections[] = [
                    'title' => 'معلومات الدفع',
                    'items' => [
                        "• طريقة الدفع: يتم دفع نصف المبلغ مقدماً والنصف الآخر نقداً عند الحضور",
                        "• يرجى إرسال صورة إيصال التحويل على رقم الواتساب: 0561667885",
                        "• بيانات الحساب البنكي:",
                        "   - البنك الأهلي السعودي",
                        "   - رقم الحساب: 18900000406701",
                        "   - الآيبان (IBAN): SA8710000018900000406701",
                        "   - رمز السويفت: NCBKSAJE"
                    ]
                ];

                $sections[] = [
                    'title' => 'موقع الاستوديو',
                    'items' => [
                        "• موقع الاستوديو: أبها، حي المحالة",
                    ]
                ];
            }

            return (new MailMessage)
                ->subject("{$statusEmoji} تحديث حالة الموعد #{$this->appointment->reference_number}")
                ->view('emails.notification', [
                    'title' => "{$statusEmoji} تحديث حالة الموعد",
                    'name' => $notifiable->name,
                    'greeting' => "مرحباً {$notifiable->name}!",
                    'intro' => "تم تحديث حالة موعدك إلى: {$statusEmoji} {$status}",
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
        } catch (Throwable $e) {
            Log::error('Error preparing appointment status update email', [
                'error' => $e->getMessage(),
                'appointment_reference' => $this->appointment->reference_number
            ]);
            throw $e;
        }
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'تحديث حالة الموعد',
            'message' => "تم تحديث حالة الموعد إلى: {$this->appointment->status}",
            'type' => 'appointment_status_updated',
            'appointment_reference' => $this->appointment->reference_number,
            'status' => $this->appointment->status
        ];
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
