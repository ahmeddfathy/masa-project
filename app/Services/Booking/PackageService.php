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
        return $packages->each(function($package) {
            $package->service_ids = $package->services->pluck('id')->toArray();
            $package->duration = floatval($package->duration);
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
