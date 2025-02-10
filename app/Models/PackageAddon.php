<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PackageAddon extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
        'package_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_addons')
            ->withPivot('quantity', 'price_at_booking')
            ->withTimestamps();
    }
}
