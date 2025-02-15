<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const SERVICE_NEW_ABAYA = 'new_abaya';
    const SERVICE_ALTERATION = 'alteration';
    const SERVICE_REPAIR = 'repair';
    const SERVICE_CUSTOM_DESIGN = 'custom_design';

    // إضافة ثوابت للموقع
    const LOCATION_STORE = 'store';
    const LOCATION_CLIENT = 'client_location';

    protected $fillable = [
        'user_id',
        'service_type',
        'appointment_date',
        'appointment_time',
        'status',
        'notes',
        'phone',
        'location',
        'address',
        'cart_item_id',
        'reference_number'  // إضافة الرقم المرجعي
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'appointment_time' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            // Generate unique reference number
            do {
                $reference = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 2) .
                                      str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT));
            } while (static::where('reference_number', $reference)->exists());

            $appointment->reference_number = $reference;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDateAttribute()
    {
        return $this->appointment_date->format('F j, Y');
    }

    public function getFormattedTimeAttribute()
    {
        return $this->appointment_time->format('g:i A');
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_APPROVED => 'تم الموافقة',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_CANCELLED => 'ملغي',
            self::STATUS_REJECTED => 'مرفوض',
            default => $this->status,
        };
    }

    public function isInStore(): bool
    {
        return $this->location === self::LOCATION_STORE;
    }

    public function isAtClientLocation(): bool
    {
        return $this->location === self::LOCATION_CLIENT;
    }

    public function getLocationTextAttribute(): string
    {
        return $this->location === self::LOCATION_STORE
            ? 'في المحل'
            : 'موقع العميل';
    }

    public function cartItem()
    {
        return $this->belongsTo(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, OrderItem::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'reference_number';
    }
}
