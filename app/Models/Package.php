<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'description',
        'base_price',
        'duration',
        'num_photos',
        'themes_count',
        'is_active'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_packages');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(PackageAddon::class, 'package_addon_pivot');
    }

    /**
     * Get the coupons that can be applied to this package.
     */
    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_package');
    }
}
