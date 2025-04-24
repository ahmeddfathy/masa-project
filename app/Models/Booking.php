<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'original_amount',
        'discount_amount',
        'image_consent',
        'terms_consent',
        'payment_transaction_id',
        'payment_id',
        'payment_status',
        'payment_method',
        'coupon_id',
        'coupon_code',
        'uuid',
        'booking_number'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'session_date' => 'date',
        'session_time' => 'datetime',
        'baby_birth_date' => 'date',
        'total_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'image_consent' => 'boolean',
        'terms_consent' => 'boolean'
    ];

    /**
     * Boot function to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // إنشاء uuid ورقم حجز عشوائي عند إنشاء حجز جديد
        static::creating(function ($booking) {
            $booking->uuid = $booking->uuid ?? (string) Str::uuid();
            $booking->booking_number = $booking->booking_number ?? static::generateBookingNumber();
        });
    }

    /**
     * توليد رقم حجز عشوائي فريد
     */
    protected static function generateBookingNumber()
    {
        // توليد رقم حجز مكون من 10 أرقام بتنسيق يسهل قراءته
        do {
            $number = 'BN-' . date('y') . '-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('booking_number', $number)->exists());

        return $number;
    }

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

    /**
     * الإعداد للاستخدام في الروابط لاستخدام UUID بدلاً من ID
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
