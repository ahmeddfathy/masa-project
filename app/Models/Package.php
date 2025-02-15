<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function addons()
    {
        return $this->hasMany(PackageAddon::class);
    }
}
