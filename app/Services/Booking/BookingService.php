<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Package;
use App\Models\PackageAddon;
use Illuminate\Support\Collection;

use App\Services\Booking\PackageService;
use App\Services\Booking\GalleryService;
use App\Services\Booking\AvailabilityService;

/**
 * خدمة إدارة الحجوزات
 * تقوم بتنسيق العمليات بين مختلف الخدمات المتعلقة بالحجز
 */
class BookingService
{
    protected $packageService;
    protected $galleryService;
    protected $availabilityService;

    public function __construct(
        PackageService $packageService,
        GalleryService $galleryService,
        AvailabilityService $availabilityService
    ) {
        $this->packageService = $packageService;
        $this->galleryService = $galleryService;
        $this->availabilityService = $availabilityService;
    }

    /**
     * جلب كل البيانات اللازمة لصفحة الحجز
     *
     * @return array يحتوي على:
     * - services: الخدمات النشطة
     * - packages: الباقات النشطة
     * - addons: الإضافات النشطة
     * - galleryImages: صور المعرض
     * - currentBookings: الحجوزات الحالية
     */
    public function getBookingPageData(): array
    {
        $services = $this->packageService->getActiveServices();
        $packages = $this->packageService->getActivePackages();
        $addons = $this->packageService->getActiveAddons($packages);
        $currentBookings = $this->availabilityService->getCurrentBookings();
        $galleryImages = $this->galleryService->getLatestImages();

        // Prepare packages data
        $this->packageService->preparePackagesData($packages);

        return compact('services', 'packages', 'addons', 'galleryImages', 'currentBookings');
    }

    /**
     * التحقق من وجود تعارض في المواعيد
     *
     * @param string $sessionDate تاريخ الجلسة
     * @param string $sessionTime وقت الجلسة
     * @param Package $package الباقة المختارة
     * @return bool true إذا كان هناك تعارض، false إذا كان الموعد متاح
     */
    public function checkBookingConflicts(string $sessionDate, string $sessionTime, Package $package): bool
    {
        return $this->availabilityService->checkBookingConflicts($sessionDate, $sessionTime, $package);
    }

    /**
     * حساب التكلفة الإجمالية للحجز
     *
     * @param Package $package الباقة المختارة
     * @param array $addonsData بيانات الإضافات المختارة
     * @return float التكلفة الإجمالية
     */
    public function calculateTotalAmount(Package $package, array $addonsData = []): float
    {
        return $this->packageService->calculateTotalAmount($package, $addonsData);
    }

    /**
     * إنشاء حجز جديد
     *
     * @param array $data بيانات الحجز
     * @param float $totalAmount التكلفة الإجمالية
     * @param int $userId معرف المستخدم
     * @return Booking
     */
    public function createBooking(array $data, float $totalAmount, int $userId): Booking
    {
        $booking = Booking::create([
            'user_id' => $userId,
            'service_id' => $data['service_id'],
            'package_id' => $data['package_id'],
            'session_date' => $data['session_date'],
            'session_time' => $data['session_time'],
            'baby_name' => $data['baby_name'],
            'baby_birth_date' => $data['baby_birth_date'],
            'gender' => $data['gender'],
            'notes' => $data['notes'],
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'booking_date' => now(),
            'image_consent' => $data['image_consent']
        ]);

        if (!empty($data['addons'])) {
            $this->attachAddons($booking, $data['addons']);
        }

        return $booking;
    }

    /**
     * إضافة الإضافات للحجز
     *
     * @param Booking $booking الحجز
     * @param array $addonsData بيانات الإضافات
     */
    private function attachAddons(Booking $booking, array $addonsData): void
    {
        foreach ($addonsData as $addonData) {
            if (isset($addonData['id'])) {
                $addon = PackageAddon::findOrFail($addonData['id']);
                $booking->addons()->attach($addon->id, [
                    'quantity' => $addonData['quantity'] ?? 1,
                    'price_at_booking' => $addon->price
                ]);
            }
        }
    }
}
