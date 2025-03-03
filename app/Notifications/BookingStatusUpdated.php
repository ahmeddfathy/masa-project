<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;
use Carbon\Carbon;

class BookingStatusUpdated extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
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
            $statusEmoji = match($this->booking->status) {
                'pending' => '⏳',
                'confirmed' => '✅',
                'cancelled' => '❌',
                'completed' => '🎉',
                'no_show' => '❗',
                'rescheduled' => '🔄',
                default => '📝'
            };

            $status = match($this->booking->status) {
                'pending' => 'قيد الانتظار',
                'confirmed' => 'مؤكد',
                'cancelled' => 'ملغي',
                'completed' => 'مكتمل',
                'no_show' => 'لم يحضر',
                'rescheduled' => 'معاد جدولته',
                default => $this->booking->status
            };

            $sections = [
                [
                    'title' => 'تفاصيل الحجز',
                    'items' => [
                        "• رقم الحجز: #{$this->booking->booking_number}",
                        "• الخدمة: {$this->booking->service->name}",
                        "• الباقة: {$this->booking->package->name}",
                        "• التاريخ: " . $this->booking->session_date->format('Y-m-d'),
                        "• الوقت: " . $this->booking->session_time->format('H:i'),
                    ]
                ],
                [
                    'title' => 'معلومات الدفع',
                    'items' => [
                        'المبلغ الإجمالي: 💰 ' . number_format($this->booking->total_amount, 2) . ' ر.س',
                        'حالة الدفع: ' . ($this->booking->payment_status === 'paid' ? '✅ مدفوع' : '⏳ قيد الانتظار')
                    ]
                ]
            ];

            if ($this->booking->notes) {
                $sections[] = [
                    'title' => 'ملاحظات',
                    'items' => [
                        $this->booking->notes
                    ]
                ];
            }

            if ($this->booking->status === 'confirmed') {
                $halfAmount = number_format($this->booking->total_amount / 2, 2);
                $sections[] = [
                    'title' => 'تذكير بمعلومات الدفع',
                    'items' => [
                        "• يرجى دفع نصف المبلغ ({$halfAmount} ر.س) مقدماً لتأكيد الحجز",
                        "• يمكنك إرسال صورة إيصال التحويل على رقم الواتساب: 0561667885",
                        "• بيانات الحساب البنكي:",
                        "   - البنك الأهلي السعودي",
                        "   - رقم الحساب: 18900000406701",
                        "   - الآيبان (IBAN): SA8710000018900000406701",
                        "   - رمز السويفت: NCBKSAJE"
                    ]
                ];

                $sections[] = [
                    'title' => 'تذكير بمعلومات الموقع',
                    'items' => [
                        "• موقع الاستوديو: أبها، حي المحالة",
                        // معلومات إضافية عن كيفية الوصول للاستوديو
                    ]
                ];
            }

            return (new MailMessage)
                ->subject("{$statusEmoji} تحديث حالة الحجز #{$this->booking->booking_number}")
                ->view('emails.notification', [
                    'title' => "{$statusEmoji} تحديث حالة الحجز",
                    'name' => $notifiable->name,
                    'greeting' => "مرحباً {$notifiable->name}!",
                    'intro' => "تم تحديث حالة حجزك إلى: {$statusEmoji} {$status}",
                    'content' => [
                        'sections' => $sections,
                        'action' => [
                            'text' => '👉 تفاصيل الحجز',
                            'url' => route('client.bookings.show', $this->booking->uuid)
                        ],
                        'outro' => [
                            '🙏 شكراً لاختيارك خدماتنا!',
                            '📞 إذا كان لديك أي استفسارات، لا تتردد في الاتصال بنا.'
                        ]
                    ]
                ]);
        } catch (Throwable $e) {
            Log::error('Error preparing booking status email', [
                'error' => $e->getMessage(),
                'booking_number' => $this->booking->booking_number
            ]);
            throw $e;
        }
    }

    public function toArray($notifiable): array
    {
        try {
            $status = match($this->booking->status) {
                'pending' => 'قيد الانتظار',
                'confirmed' => 'مؤكد',
                'cancelled' => 'ملغي',
                'completed' => 'مكتمل',
                'no_show' => 'لم يحضر',
                'rescheduled' => 'معاد جدولته',
                default => $this->booking->status
            };

            return [
                'title' => 'تحديث حالة الحجز',
                'message' => "تم تحديث حالة الحجز رقم #{$this->booking->booking_number} إلى {$status}",
                'type' => 'booking_status_updated',
                'booking_number' => $this->booking->booking_number,
                'uuid' => $this->booking->uuid,
                'status' => $this->booking->status,
                'status_text' => $status
            ];
        } catch (Throwable $e) {
            Log::error('Error in toArray method', [
                'error' => $e->getMessage(),
                'booking_number' => $this->booking->booking_number
            ]);

            return [
                'title' => 'تحديث حالة الحجز',
                'message' => 'حدث خطأ أثناء معالجة الإشعار',
                'type' => 'booking_status_updated',
                'booking_number' => $this->booking->booking_number,
                'status' => $this->booking->status ?? 'unknown'
            ];
        }
    }

    public function failed(Throwable $e)
    {
        Log::error('Failed to send booking status notification', [
            'error' => $e->getMessage(),
            'error_trace' => $e->getTraceAsString(),
            'booking_number' => $this->booking->booking_number ?? null,
            'booking_data' => [
                'status' => $this->booking->status ?? null,
                'date' => $this->booking->session_date ?? null,
                'time' => $this->booking->session_time ?? null
            ]
        ]);
    }
}
