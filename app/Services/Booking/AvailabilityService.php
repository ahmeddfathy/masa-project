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

            // تنظيف وتنسيق الوقت
            if (!preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $sessionTime)) {
                return true;
            }

            $timeComponents = explode(':', $sessionTime);
            $cleanTime = sprintf('%02d:%02d', (int)$timeComponents[0], (int)$timeComponents[1]);

            // تنسيق التاريخ والوقت بشكل صحيح
            $sessionStartTime = Carbon::parse($sessionDate . ' ' . $cleanTime);
            $sessionEndTime = $sessionStartTime->copy()->addHours($package->duration);

            // التحقق من أن الموعد ضمن ساعات العمل
            if (!$this->isWithinWorkingHours($sessionStartTime, $sessionEndTime)) {
                return true;
            }

            $conflictingBookings = Booking::where('status', '!=', 'cancelled')
                ->where(function($query) use ($sessionDate, $sessionStartTime, $sessionEndTime) {
                    $query->where('session_date', $sessionDate)
                        ->orWhereBetween('session_date', [
                            $sessionStartTime->format('Y-m-d'),
                            $sessionEndTime->format('Y-m-d')
                        ]);
                })
                ->with('package:id,duration')
                ->get()
                ->filter(function ($booking) use ($sessionStartTime, $sessionEndTime) {
                    try {
                        if (empty($booking->session_time)) {
                            return false;
                        }

                        $existingDateTime = Carbon::parse($booking->session_time);
                        $bookingStart = Carbon::parse($booking->session_date)->setHour($existingDateTime->hour)->setMinute($existingDateTime->minute);
                        $bookingEnd = $bookingStart->copy()->addHours($booking->package->duration);

                        return $sessionStartTime->lt($bookingEnd) && $sessionEndTime->gt($bookingStart);
                    } catch (\Exception $e) {
                        return false;
                    }
                })
                ->count();

            return $conflictingBookings >= $maxConcurrentBookings;
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

            if ($slots = $this->getAvailableTimeSlotsForDate($currentDate, $package)) {
                return [
                    'date' => $currentDate->format('Y-m-d'),
                    'slots' => $slots
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
            $interval = 30; // 30 minutes interval

            $availableSlots = [];
            $currentTime = $studioStart->copy();

            while ($currentTime->copy()->addHours($duration) <= $studioEnd) {
                $timeString = $currentTime->format('H:i');

                if (!$this->checkBookingConflicts(
                    $date->format('Y-m-d'),
                    $timeString,
                    $package
                )) {
                    $availableSlots[] = [
                        'time' => $timeString,
                        'end_time' => $currentTime->copy()->addHours($duration)->format('H:i'),
                        'formatted_time' => $this->formatTimeInArabic($timeString)
                    ];
                }

                $currentTime->addMinutes($interval);
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
