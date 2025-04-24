<?php

namespace App\Services\Booking;

use App\Models\Package;
use App\Models\PackageAddon;
use App\Models\Service;
use Illuminate\Support\Collection;

class PackageService
{
    public function getActiveServices(): Collection
    {
        return Service::where('is_active', true)->get();
    }

    public function getActivePackages(): Collection
    {
        return Package::where('is_active', true)
            ->with(['addons' => function($query) {
                $query->where('is_active', true);
            }])
            ->get();
    }

    public function getActiveAddons(Collection $packages): Collection
    {
        return PackageAddon::where('is_active', true)
            ->whereHas('packages', function($query) use ($packages) {
                $query->whereIn('packages.id', $packages->pluck('id'));
            })
            ->get();
    }

    public function preparePackagesData(Collection $packages): Collection
    {
        // جلب الكوبونات النشطة
        $activeCoupons = $this->getActivePackageCoupons();

        return $packages->each(function($package) use ($activeCoupons) {
            $package->service_ids = $package->services->pluck('id')->toArray();
            $package->duration = floatval($package->duration);

            // إضافة الكوبونات المتاحة لكل باقة
            $package->applicable_coupons = $this->getApplicableCouponsForPackage($package, $activeCoupons);
            if (count($package->applicable_coupons) > 0) {
                $bestCoupon = $package->applicable_coupons->sortByDesc(function($coupon) use ($package) {
                    if ($coupon->type === 'percentage') {
                        return $package->base_price * ($coupon->value / 100);
                    } else {
                        return min($coupon->value, $package->base_price);
                    }
                })->first();

                $package->best_coupon = $bestCoupon;

                // حساب قيمة الخصم للعرض فقط (دون تغيير السعر الأصلي)
                if ($bestCoupon->type === 'percentage') {
                    $discountAmount = $package->base_price * ($bestCoupon->value / 100);
                    $package->discount_text = $bestCoupon->value . '%';
                } else {
                    $discountAmount = min($bestCoupon->value, $package->base_price);
                    $package->discount_text = $discountAmount . ' ريال';
                }
                $package->discount_amount = $discountAmount;
            }
        });
    }

    /**
     * الحصول على الكوبونات النشطة التي يمكن تطبيقها على الباقات
     *
     * @return Collection
     */
    protected function getActivePackageCoupons(): Collection
    {
        $now = now();

        return \App\Models\Coupon::where('is_active', true)
            ->where('applies_to_packages', true)
            ->where(function($query) use ($now) {
                $query->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', $now);
            })
            ->where(function($query) {
                $query->whereNull('max_uses')
                      ->orWhereRaw('used_count < max_uses');
            })
            ->with('packages')
            ->get();
    }

    /**
     * الحصول على الكوبونات المتاحة لباقة محددة
     *
     * @param Package $package
     * @param Collection $activeCoupons
     * @return Collection
     */
    protected function getApplicableCouponsForPackage(Package $package, Collection $activeCoupons): Collection
    {
        // تصفية الكوبونات التي تنطبق على هذه الباقة
        return $activeCoupons->filter(function($coupon) use ($package) {
            return $coupon->appliesToPackage($package->id) &&
                   $package->base_price >= $coupon->min_order_amount;
        });
    }

    public function calculateTotalAmount(Package $package, array $addonsData = []): float
    {
        $totalAmount = $package->base_price;

        if (!empty($addonsData)) {
            foreach ($addonsData as $addonData) {
                if (isset($addonData['id'])) {
                    $addon = PackageAddon::findOrFail($addonData['id']);
                    $quantity = $addonData['quantity'] ?? 1;
                    $totalAmount += ($addon->price * $quantity);
                }
            }
        }

        return $totalAmount;
    }
}
