<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'package_id',
        'booking_date',
        'session_date',
        'session_time',
        'baby_name',
        'baby_birth_date',
        'gender',
        'notes',
        'status',
        'total_amount',
        'image_consent',
        'terms_consent'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'session_date' => 'date',
        'session_time' => 'datetime',
        'baby_birth_date' => 'date',
        'total_amount' => 'decimal:2',
        'image_consent' => 'boolean',
        'terms_consent' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(PackageAddon::class, 'booking_addons', 'booking_id', 'addon_id')
            ->withPivot('quantity', 'price_at_booking')
            ->withTimestamps();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
