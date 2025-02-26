<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    protected const STUDIO_START_TIME = '10:00';
    protected const STUDIO_END_TIME = '18:00';
    protected const MAX_DAYS_AHEAD = 30;

    public function getCurrentBookings(): Collection
    {
        try {
            return Booking::where('status', '!=', 'cancelled')
                ->where('session_date', '>=', now()->format('Y-m-d'))
                ->where('session_date', '<=', now()->addDays(self::MAX_DAYS_AHEAD)->format('Y-m-d'))
                ->with('package:id,duration')
                ->get()
                ->map(function($booking) {
                    try {
                        if (empty($booking->session_time)) {
                            return null;
                        }

                        $existingDateTime = Carbon::parse($booking->session_time);
                        $startTime = Carbon::parse($booking->session_date)->setHour($existingDateTime->hour)->setMinute($existingDateTime->minute);
                        $endTime = $startTime->copy()->addMinutes($booking->package->duration);

                        return [
                            'id' => $booking->id,
                            'date' => $booking->session_date,
                            'time' => $startTime->format('H:i'),
                            'endTime' => $endTime->format('H:i'),
                            'duration' => $booking->package->duration,
                            'start_datetime' => $startTime->format('Y-m-d H:i'),
                            'end_datetime' => $endTime->format('Y-m-d H:i')
                        ];
                    } catch (\Exception $e) {
                        return null;
                    }
                })
                ->filter()
                ->values();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function checkBookingConflicts(string $sessionDate, string $sessionTime, Package $package): bool
    {
        try {
            $maxConcurrentBookings = (int)Setting::get('max_concurrent_bookings', 1);

            if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $sessionTime)) {
                return true;
            }

            $timeComponents = explode(':', $sessionTime);
            $cleanTime = sprintf('%02d:%02d', (int)$timeComponents[0], (int)$timeComponents[1]);

            // تنسيق التاريخ والوقت بشكل صحيح
            $sessionStartTime = Carbon::parse($sessionDate . ' ' . $cleanTime);
            $sessionEndTime = $sessionStartTime->copy()->addMinutes($package->duration);

            if (!$this->isWithinWorkingHours($sessionStartTime, $sessionEndTime)) {
                return true;
            }

            // جلب جميع الحجوزات في نفس اليوم
            $existingBookings = Booking::where('status', '!=', 'cancelled')
                ->where('session_date', $sessionDate)
                ->with('package:id,duration')
                ->get();

            // التحقق من التعارضات في كل فترة زمنية
            $timeSlots = [];
            foreach ($existingBookings as $booking) {
                try {
                    if (empty($booking->session_time)) {
                        continue;
                    }

                    $existingDateTime = Carbon::parse($booking->session_time);
                    $bookingStart = Carbon::parse($booking->session_date)
                        ->setHour($existingDateTime->hour)
                        ->setMinute($existingDateTime->minute);
                    $bookingEnd = $bookingStart->copy()->addMinutes($booking->package->duration);

                    // إضافة كل فترة 30 دقيقة من الحجز إلى المصفوفة
                    $current = $bookingStart->copy();
                    while ($current < $bookingEnd) {
                        $timeKey = $current->format('H:i');
                        if (!isset($timeSlots[$timeKey])) {
                            $timeSlots[$timeKey] = 0;
                        }
                        $timeSlots[$timeKey]++;
                        $current->addMinutes(30);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // التحقق من الفترات المطلوبة للحجز الجديد
            $newBookingStart = $sessionStartTime->copy();
            while ($newBookingStart < $sessionEndTime) {
                $timeKey = $newBookingStart->format('H:i');
                if (isset($timeSlots[$timeKey]) && $timeSlots[$timeKey] >= $maxConcurrentBookings) {
                    return true; // يوجد تعارض
                }
                $newBookingStart->addMinutes(30);
            }

            return false; // لا يوجد تعارض
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getNextAvailableSlot(Package $package, ?string $fromDate = null): ?array
    {
        $fromDate = $fromDate ? Carbon::parse($fromDate) : Carbon::today();
        $maxDays = self::MAX_DAYS_AHEAD;

        for ($day = 0; $day <= $maxDays; $day++) {
            $currentDate = $fromDate->copy()->addDays($day);
            $slots = $this->getAvailableTimeSlotsForDate($currentDate, $package);

            if (!empty($slots)) {
                return [
                    'date' => $currentDate->format('Y-m-d'),
                    'formatted_date' => $currentDate->translatedFormat('l j F Y'),
                    'slots' => $slots,
                    'is_next_available' => $day > 0
                ];
            }
        }

        return null;
    }

    public function getAvailableTimeSlotsForDate(Carbon $date, Package $package, bool $checkAlternatives = true): array
    {
        try {
            $studioStart = Carbon::createFromFormat('H:i', self::STUDIO_START_TIME);
            $studioEnd = Carbon::createFromFormat('H:i', self::STUDIO_END_TIME);
            $durationInMinutes = $package->duration;
            $maxConcurrentBookings = (int)Setting::get('max_concurrent_bookings', 1);

            // جلب الحجوزات الموجودة في نفس اليوم
            $existingBookings = Booking::where('status', '!=', 'cancelled')
                ->where('session_date', $date->format('Y-m-d'))
                ->with('package:id,duration')
                ->get()
                ->sortBy(function($booking) {
                    return Carbon::parse($booking->session_time)->format('H:i');
                });

            // تحويل الحجوزات إلى فترات زمنية محجوزة
            $bookedPeriods = [];
            foreach ($existingBookings as $booking) {
                if (empty($booking->session_time)) continue;

                $bookingStart = Carbon::parse($booking->session_time);
                $bookingEnd = $bookingStart->copy()->addMinutes($booking->package->duration);

                $bookedPeriods[] = [
                    'start' => $bookingStart->format('H:i'),
                    'end' => $bookingEnd->format('H:i')
                ];
            }

            // البحث عن الفترات المتاحة
            $availableSlots = [];
            $currentTime = $studioStart->copy();

            // إذا كان اليوم هو اليوم الحالي، نبدأ من الوقت الحالي
            if ($date->format('Y-m-d') === Carbon::today()->format('Y-m-d')) {
                $now = Carbon::now();
                if ($now->format('H:i') > $studioStart->format('H:i')) {
                    $currentTime = $this->roundTimeUp($now->copy(), 30);
                }
            }

            // البحث عن أول وقت متاح بعد آخر حجز
            if (!empty($bookedPeriods)) {
                $lastBooking = end($bookedPeriods);
                $lastBookingEnd = Carbon::createFromFormat('H:i', $lastBooking['end']);
                if ($lastBookingEnd > $currentTime) {
                    $currentTime = $lastBookingEnd;
                }
            }

            while ($currentTime->copy()->addMinutes($durationInMinutes) <= $studioEnd) {
                $slotStart = $currentTime->format('H:i');
                $slotEnd = $currentTime->copy()->addMinutes($durationInMinutes)->format('H:i');

                $isAvailable = true;
                $concurrentBookings = 0;

                // التحقق من تداخل الحجوزات
                foreach ($bookedPeriods as $period) {
                    if (
                        ($slotStart >= $period['start'] && $slotStart < $period['end']) ||
                        ($slotEnd > $period['start'] && $slotEnd <= $period['end']) ||
                        ($slotStart <= $period['start'] && $slotEnd >= $period['end'])
                    ) {
                        $concurrentBookings++;
                        if ($concurrentBookings >= $maxConcurrentBookings) {
                            $isAvailable = false;
                            break;
                        }
                    }
                }

                if ($isAvailable) {
                    $availableSlots[] = [
                        'time' => $slotStart,
                        'end_time' => $slotEnd,
                        'formatted_time' => $this->formatTimeInArabic($slotStart)
                    ];
                }

                // تحريك الوقت للموعد التالي المحتمل
                if (!empty($bookedPeriods)) {
                    // البحث عن أقرب نهاية حجز بعد الوقت الحالي
                    $nextEnd = null;
                    foreach ($bookedPeriods as $period) {
                        $periodEnd = Carbon::createFromFormat('H:i', $period['end']);
                        if ($periodEnd > $currentTime && ($nextEnd === null || $periodEnd < $nextEnd)) {
                            $nextEnd = $periodEnd;
                        }
                    }

                    if ($nextEnd !== null && $nextEnd > $currentTime) {
                        $currentTime = $nextEnd;
                    } else {
                        $currentTime->addMinutes($durationInMinutes);
                    }
                } else {
                    $currentTime->addMinutes($durationInMinutes);
                }
            }

            if (empty($availableSlots)) {
                $response = [
                    'has_alternative_packages' => false,
                    'alternative_packages' => null,
                    'next_available_date' => null,
                    'next_available_formatted_date' => null,
                    'next_available_slots' => null
                ];

                if ($checkAlternatives) {
                    $alternativePackages = $this->findAvailablePackages($date, $package);
                    // فقط إذا وجدنا باقات بديلة متاحة فعلاً
                    if ($alternativePackages && !empty($alternativePackages)) {
                        $response['has_alternative_packages'] = true;
                        $response['alternative_packages'] = $alternativePackages;
                    }
                }

                $nextAvailable = $this->getNextAvailableSlot($package, $date->addDay()->format('Y-m-d'));
                if ($nextAvailable) {
                    $response['next_available_date'] = $nextAvailable['date'];
                    $response['next_available_formatted_date'] = $nextAvailable['formatted_date'];
                    $response['next_available_slots'] = $nextAvailable['slots'];
                }

                return $response;
            }

            return $availableSlots;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function isWithinWorkingHours(Carbon $start, Carbon $end): bool
    {
        $studioStart = Carbon::createFromFormat('H:i', self::STUDIO_START_TIME);
        $studioEnd = Carbon::createFromFormat('H:i', self::STUDIO_END_TIME);

        return $start->format('H:i') >= $studioStart->format('H:i') &&
               $end->format('H:i') <= $studioEnd->format('H:i');
    }

    /**
     * تقريب الوقت لأعلى بمقدار الدقائق المحددة
     * @param Carbon $time
     * @param int $minutes
     * @return Carbon
     */
    protected function roundTimeUp(Carbon $time, int $minutes = 30): Carbon
    {
        $seconds = $minutes * 60;
        $timestamp = $time->timestamp;
        $remainder = $timestamp % $seconds;

        // إذا كان الوقت بالفعل منقسماً على عدد الدقائق، نرجع نفس الوقت
        if ($remainder === 0) {
            return $time->copy();
        }

        // وإلا، نقرب لأعلى
        return $time->copy()->addSeconds($seconds - $remainder);
    }

    protected function formatTimeInArabic(string $time): string
    {
        $hour = (int)substr($time, 0, 2);
        $minute = substr($time, 3, 2);
        $period = 'صباحاً';

        if ($hour >= 12) {
            $displayHour = $hour > 12 ? $hour - 12 : 12;
            $period = $hour >= 12 && $hour < 15 ? 'ظهراً' : 'عصراً';
        } else {
            $displayHour = $hour;
        }

        return sprintf('%d:%s %s', $displayHour, $minute, $period);
    }

    public function findAvailablePackages(Carbon $date, Package $requestedPackage): ?array
    {
        try {
            // إنشاء كائنات للوقت
            $studioStart = Carbon::createFromFormat('H:i', self::STUDIO_START_TIME);
            $studioEnd = Carbon::createFromFormat('H:i', self::STUDIO_END_TIME);
            $maxConcurrentBookings = (int)Setting::get('max_concurrent_bookings', 1);

            // جلب جميع الحجوزات في التاريخ المطلوب
            $existingBookings = Booking::where('status', '!=', 'cancelled')
                ->where('session_date', $date->format('Y-m-d'))
                ->with('package:id,duration')
                ->get();

            // إذا لم تكن هناك حجوزات، تحقق فقط من وقت العمل المتبقي
            if ($existingBookings->isEmpty()) {
                // إذا كان اليوم هو اليوم الحالي وتجاوزنا وقت البدء، نضبط وقت البدء
                $currentStart = $studioStart->copy();
                if ($date->format('Y-m-d') === Carbon::today()->format('Y-m-d')) {
                    $now = Carbon::now();
                    if ($now->format('H:i') > $studioStart->format('H:i')) {
                        $currentStart = $this->roundTimeUp($now->copy(), 30);
                    }
                }

                // تحقق من وقت العمل المتبقي
                $remainingTime = $currentStart->diffInMinutes($studioEnd);

                // إذا كان الوقت المتبقي أقل من مدة الباقة المطلوبة، لا توجد مواعيد متاحة
                if ($remainingTime < $requestedPackage->duration) {
                    return null;
                }

                // جلب باقات بديلة قد تكون مناسبة
                $alternativePackages = Package::where('is_active', true)
                    ->whereHas('services', function($query) use ($requestedPackage) {
                        $query->whereIn('services.id', $requestedPackage->services->pluck('id'));
                    })
                    ->where('id', '!=', $requestedPackage->id)
                    ->where('duration', '<=', $remainingTime) // فقط الباقات التي يمكن استيعابها في الوقت المتبقي
                    ->orderBy('duration', 'desc')
                    ->limit(2)
                    ->get();

                if ($alternativePackages->isEmpty()) {
                    return null;
                }

                // ترجع البيانات المناسبة
                $availablePackages = [];
                foreach ($alternativePackages as $package) {
                    $availablePackages[] = [
                        'package' => $package,
                        'available_slots' => [[
                            'time' => $currentStart->format('H:i'),
                            'end_time' => $currentStart->copy()->addMinutes($package->duration)->format('H:i'),
                            'formatted_time' => $this->formatTimeInArabic($currentStart->format('H:i'))
                        ]]
                    ];
                }

                return $availablePackages;
            }

            // تحويل البيانات إلى مصفوفة من الفترات المحجوزة
            $bookedTimeSlots = [];

            foreach ($existingBookings as $booking) {
                if (empty($booking->session_time)) continue;

                $startTime = Carbon::parse($booking->session_time);
                $endTime = $startTime->copy()->addMinutes($booking->package->duration);

                // تقسيم الفترة إلى فترات مدة كل منها 30 دقيقة
                $currentSlot = $startTime->copy();
                while ($currentSlot < $endTime) {
                    $slotKey = $currentSlot->format('H:i');
                    if (!isset($bookedTimeSlots[$slotKey])) {
                        $bookedTimeSlots[$slotKey] = 0;
                    }
                    $bookedTimeSlots[$slotKey]++;
                    $currentSlot->addMinutes(30);
                }
            }

            // تحديد ساعات العمل كاملة
            $allTimeSlots = [];
            $currentSlot = $studioStart->copy();

            // إذا كان اليوم هو اليوم الحالي وتجاوزنا وقت البدء، نضبط وقت البدء
            if ($date->format('Y-m-d') === Carbon::today()->format('Y-m-d')) {
                $now = Carbon::now();
                if ($now->format('H:i') > $currentSlot->format('H:i')) {
                    $currentSlot = $this->roundTimeUp($now->copy(), 30);
                }
            }

            // إنشاء قائمة بجميع الفترات الزمنية في ساعات العمل
            while ($currentSlot < $studioEnd) {
                $slotKey = $currentSlot->format('H:i');
                $allTimeSlots[$slotKey] = isset($bookedTimeSlots[$slotKey]) ? $bookedTimeSlots[$slotKey] : 0;
                $currentSlot->addMinutes(30);
            }

            // الآن لدينا مصفوفة بجميع الفترات المتاحة وغير المتاحة

            // جلب الباقات البديلة المحتملة
            $alternativePackages = Package::where('is_active', true)
                ->whereHas('services', function($query) use ($requestedPackage) {
                    $query->whereIn('services.id', $requestedPackage->services->pluck('id'));
                })
                ->where('id', '!=', $requestedPackage->id)
                ->orderBy('duration', 'desc')
                ->get();

            $availablePackages = [];

            // للتحقق من كل باقة بديلة
            foreach ($alternativePackages as $package) {
                $requiredSlots = ceil($package->duration / 30); // عدد الفترات المطلوبة (كل فترة 30 دقيقة)
                $availableSlots = [];

                // التحقق من كل وقت بداية محتمل
                $startSlot = $studioStart->copy();

                // ضبط وقت البداية إذا كان اليوم هو اليوم الحالي
                if ($date->format('Y-m-d') === Carbon::today()->format('Y-m-d')) {
                    $now = Carbon::now();
                    if ($now->format('H:i') > $startSlot->format('H:i')) {
                        $startSlot = $this->roundTimeUp($now->copy(), 30);
                    }
                }

                while ($startSlot->copy()->addMinutes($package->duration) <= $studioEnd) {
                    $isAvailable = true;

                    // التحقق من توفر الوقت المطلوب بالكامل
                    $checkSlot = $startSlot->copy();
                    for ($i = 0; $i < $requiredSlots; $i++) {
                        $slotKey = $checkSlot->format('H:i');

                        // إذا تجاوزنا الحد الأقصى للحجوزات المتزامنة، الفترة غير متاحة
                        if (isset($allTimeSlots[$slotKey]) && $allTimeSlots[$slotKey] >= $maxConcurrentBookings) {
                            $isAvailable = false;
                            break;
                        }

                        $checkSlot->addMinutes(30);
                    }

                    if ($isAvailable) {
                        $endTime = $startSlot->copy()->addMinutes($package->duration);
                        $availableSlots[] = [
                            'time' => $startSlot->format('H:i'),
                            'end_time' => $endTime->format('H:i'),
                            'formatted_time' => $this->formatTimeInArabic($startSlot->format('H:i'))
                        ];
                    }

                    $startSlot->addMinutes(30);
                }

                // إذا وجدنا أوقات متاحة، نضيف الباقة إلى النتائج
                if (!empty($availableSlots)) {
                    $availablePackages[] = [
                        'package' => $package,
                        'available_slots' => $availableSlots
                    ];

                    // نكتفي بباقتين كحد أقصى
                    if (count($availablePackages) >= 2) {
                        break;
                    }
                }
            }

            return !empty($availablePackages) ? $availablePackages : null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in findAvailablePackages: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return null;
        }
    }
}
