<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserCouponUsage extends Model
{
    use HasFactory;

    /**
     * اسم الجدول المرتبط بالنموذج.
     *
     * @var string
     */
    protected $table = 'user_coupon_usage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'coupon_id',
        'usable_type',
        'usable_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the usage record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the coupon that is associated with the usage record.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the owning usable model.
     */
    public function usable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if a user has already used a coupon for a specific model.
     *
     * @param int $userId
     * @param int $couponId
     * @param string $type
     * @return bool
     */
    public static function hasUserUsedCoupon(int $userId, int $couponId, string $type = 'App\\Models\\Order'): bool
    {
        return self::where('user_id', $userId)
            ->where('coupon_id', $couponId)
            ->where('usable_type', $type)
            ->exists();
    }

    /**
     * Record usage of a coupon.
     *
     * @param int $userId
     * @param int $couponId
     * @param Model $usable
     * @return self
     */
    public static function recordUsage(int $userId, int $couponId, Model $usable): self
    {
        return self::create([
            'user_id' => $userId,
            'coupon_id' => $couponId,
            'usable_type' => get_class($usable),
            'usable_id' => $usable->id
        ]);
    }
}
