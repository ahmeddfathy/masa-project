<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'service_packages');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
