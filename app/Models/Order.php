<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_CARD = 'card';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';

    const ORDER_STATUS_PENDING = 'pending';
    const ORDER_STATUS_PROCESSING = 'processing';
    const ORDER_STATUS_COMPLETED = 'completed';
    const ORDER_STATUS_CANCELLED = 'cancelled';
    const ORDER_STATUS_FAILED = 'failed';
    const ORDER_STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const ORDER_STATUS_ON_THE_WAY = 'on_the_way';
    const ORDER_STATUS_DELIVERED = 'delivered';
    const ORDER_STATUS_RETURNED = 'returned';

    protected $fillable = [
        'user_id',
        'total_amount',
        'shipping_address',
        'phone',
        'payment_method',
        'payment_status',
        'order_status',
        'notes',
        'policy_agreement',
        'uuid',
        'order_number',
        'payment_transaction_id',
        'payment_id',
        'amount_paid',
    ];

    protected $casts = [
        'total_amount' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->uuid = (string) Str::uuid();
            $order->order_number = 'ORD-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helper methods for status checks
    public function isPending(): bool
    {
        return $this->order_status === self::ORDER_STATUS_PENDING;
    }

    public function isProcessing(): bool
    {
        return $this->order_status === self::ORDER_STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return $this->order_status === self::ORDER_STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->order_status === self::ORDER_STATUS_CANCELLED;
    }

    public function isPaymentPending(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PENDING;
    }

    public function isPaymentPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function isPaymentFailed(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_FAILED;
    }

    // Helper methods for new status checks
    public function isOutForDelivery(): bool
    {
        return $this->order_status === self::ORDER_STATUS_OUT_FOR_DELIVERY;
    }

    public function isOnTheWay(): bool
    {
        return $this->order_status === self::ORDER_STATUS_ON_THE_WAY;
    }

    public function isDelivered(): bool
    {
        return $this->order_status === self::ORDER_STATUS_DELIVERED;
    }

    public function isReturned(): bool
    {
        return $this->order_status === self::ORDER_STATUS_RETURNED;
    }

    // Add this method to use uuid in routes
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
