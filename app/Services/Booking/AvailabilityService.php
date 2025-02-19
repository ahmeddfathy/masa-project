<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    protected const STUDIO_START_TIME = '10:30';
    protected const STUDIO_END_TIME = '17:00';
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
                        $endTime = $startTime->copy()->addHours($booking->package->duration);

                        return [
                            'id' => $booking->id,
                            'date' => $booking->session_date,
                            'time' => $startTime->format('H:i'),
                            'endTime' => $endTime->format('H:i'),
                            'duration' => floatval($booking->package->duration),
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
            $sessionEndTime = $sessionStartTime->copy()->addHours($package->duration);

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
                    $bookingEnd = $bookingStart->copy()->addHours($booking->package->duration);

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

    public function getAvailableTimeSlotsForDate(Carbon $date, Package $package): array
    {
        try {
            $studioStart = Carbon::createFromFormat('H:i', self::STUDIO_START_TIME);
            $studioEnd = Carbon::createFromFormat('H:i', self::STUDIO_END_TIME);
            $duration = $package->duration;
            $maxConcurrentBookings = (int)Setting::get('max_concurrent_bookings', 1);

            // جلب جميع الحجوزات في نفس اليوم وترتيبها حسب الوقت
            $existingBookings = Booking::where('status', '!=', 'cancelled')
                ->where('session_date', $date->format('Y-m-d'))
                ->with('package:id,duration')
                ->get()
                ->map(function($booking) {
                    if (empty($booking->session_time)) {
                        return null;
                    }

                    $existingDateTime = Carbon::parse($booking->session_time);
                    $startTime = Carbon::parse($booking->session_date)
                        ->setHour($existingDateTime->hour)
                        ->setMinute($existingDateTime->minute);

                    return [
                        'start_time' => $startTime->format('H:i'),
                        'end_time' => $startTime->copy()->addHours($booking->package->duration)->format('H:i'),
                        'duration' => $booking->package->duration
                    ];
                })
                ->filter()
                ->values();

            // تحويل الحجوزات الموجودة إلى مصفوفة للتحقق السريع
            $bookedSlots = [];
            foreach ($existingBookings as $booking) {
                $start = Carbon::createFromFormat('H:i', $booking['start_time']);
                $end = Carbon::createFromFormat('H:i', $booking['end_time']);

                $current = $start->copy();
                while ($current < $end) {
                    $timeKey = $current->format('H:i');
                    if (!isset($bookedSlots[$timeKey])) {
                        $bookedSlots[$timeKey] = 0;
                    }
                    $bookedSlots[$timeKey]++;
                    $current->addHour();
                }
            }

            $availableSlots = [];
            $currentTime = $studioStart->copy();

            // حساب المواعيد المتاحة
            while ($currentTime->copy()->addHours($duration) <= $studioEnd) {
                $timeString = $currentTime->format('H:i');
                $endTimeString = $currentTime->copy()->addHours($duration)->format('H:i');

                // التحقق من توفر المساحة في كل ساعة للحجز الجديد
                $hasSpace = true;
                $checkTime = $currentTime->copy();

                while ($checkTime < $currentTime->copy()->addHours($duration)) {
                    $timeKey = $checkTime->format('H:i');
                    if (isset($bookedSlots[$timeKey]) && $bookedSlots[$timeKey] >= $maxConcurrentBookings) {
                        $hasSpace = false;
                        // تحريك الوقت الحالي إلى أقرب ساعة متاحة
                        $currentTime = $checkTime->copy()->addHour();
                        break;
                    }
                    $checkTime->addHour();
                }

                if ($hasSpace) {
                    $availableSlots[] = [
                        'time' => $timeString,
                        'end_time' => $endTimeString,
                        'formatted_time' => $this->formatTimeInArabic($timeString)
                    ];
                    // تحريك الوقت بمقدار ساعة واحدة للبحث عن الموعد التالي المتاح
                    $currentTime->addHour();
                }
            }

            if (empty($availableSlots)) {
                $nextAvailable = $this->getNextAvailableSlot($package, $date->addDay()->format('Y-m-d'));
                if ($nextAvailable) {
                    return [
                        'next_available_date' => $nextAvailable['date'],
                        'next_available_formatted_date' => $nextAvailable['formatted_date'],
                        'next_available_slots' => $nextAvailable['slots']
                    ];
                }
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
}
