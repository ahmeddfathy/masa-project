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
            $body .= "المبلغ: " . number_format($booking->total_amount, 2) . " ر.س";

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

        $addonsArray = [];
        if ($this->booking->addons->isNotEmpty()) {
            $addonsArray = $this->booking->addons->map(function($addon) {
                return "{$addon->name} (الكمية: {$addon->pivot->quantity})";
            })->toArray();
        }

        $bookingDetails = [
            "• الخدمة: {$this->booking->service->name}",
            "• الباقة: {$this->booking->package->name}",
            "• التاريخ: " . $this->booking->session_date->format('Y-m-d'),
            "• الوقت: " . $this->booking->session_time->format('H:i')
        ];

        if ($this->booking->baby_name) {
            $bookingDetails[] = "• اسم الطفل: {$this->booking->baby_name}";
        }

        if ($this->booking->baby_birth_date) {
            $bookingDetails[] = "• تاريخ ميلاد الطفل: " . $this->booking->baby_birth_date->format('Y-m-d');
        }

        if ($this->booking->gender) {
            $bookingDetails[] = "• الجنس: " . ($this->booking->gender === 'male' ? 'ذكر' : 'أنثى');
        }

        $halfAmount = number_format($this->booking->total_amount / 2, 2);
        $sections = [
            [
                'title' => 'تفاصيل الحجز',
                'items' => $bookingDetails
            ],
            [
                'title' => 'معلومات التواصل',
                'items' => [
                    "الاسم: {$notifiable->name}",
                    "رقم الهاتف: {$notifiable->phone}"
                ]
            ],
            [
                'title' => 'معلومات الدفع',
                'items' => [
                    'المبلغ الإجمالي: 💰 ' . number_format($this->booking->total_amount, 2) . ' ر.س',
                    'حالة الدفع: ' . ($this->booking->payment_status === 'paid' ? '✅ مدفوع' : '⏳ قيد الانتظار')
                ]
            ],
            [
                'title' => 'معلومات الدفع',
                'items' => [
                    "• طريقة الدفع: يتم دفع نصف المبلغ مقدماً ({$halfAmount} ر.س) والنصف الآخر نقداً عند الحضور",
                    "• يرجى إرسال صورة إيصال التحويل على رقم الواتساب: 0561667885",
                    "• بيانات الحساب البنكي:",
                    "   - البنك الأهلي السعودي",
                    "   - رقم الحساب: 18900000406701",
                    "   - الآيبان (IBAN): SA8710000018900000406701",
                    "   - رمز السويفت: NCBKSAJE"
                ]
            ],
            [
                'title' => 'موقع الاستوديو',
                'items' => [
                    "العنوان: استوديو عدسة سوما - أبها، حي المحالة",
                    // يمكن إضافة معلومات إضافية مثل الموقع الدقيق أو إحداثيات خرائط جوجل
                ]
            ]
        ];

        if (!empty($addonsArray)) {
            $sections[] = [
                'title' => 'الإضافات',
                'items' => $addonsArray
            ];
        }

        if ($this->booking->notes) {
            $sections[] = [
                'title' => 'ملاحظات',
                'items' => [$this->booking->notes]
            ];
        }

        return (new MailMessage)
            ->subject('📸 تأكيد الحجز #' . $this->booking->booking_number)
            ->view('emails.notification', [
                'title' => '✨ تأكيد حجز التصوير',
                'name' => $notifiable->name,
                'greeting' => "مرحباً {$notifiable->name}!",
                'intro' => 'نشكرك على حجزك! تم تأكيد موعد التصوير بنجاح.',
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
