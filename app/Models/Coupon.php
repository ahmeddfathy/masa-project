<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'used_count',
        'is_active',
        'applies_to_all_products',
        'applies_to_products',
        'applies_to_packages',
        'starts_at',
        'expires_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'float',
        'min_order_amount' => 'float',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'applies_to_all_products' => 'boolean',
        'applies_to_products' => 'boolean',
        'applies_to_packages' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the products that this coupon can be applied to.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

    /**
     * Get the packages that this coupon can be applied to.
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'coupon_package');
    }

    /**
     * Get the user usage records for this coupon.
     */
    public function userUsages(): HasMany
    {
        return $this->hasMany(UserCouponUsage::class);
    }

    /**
     * Check if the coupon is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Check if the current authenticated user has already used this coupon.
     *
     * @param string $type
     * @return bool
     */
    public function hasBeenUsedByCurrentUser(string $type = 'App\\Models\\Order'): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return UserCouponUsage::hasUserUsedCoupon(Auth::id(), $this->id, $type);
    }

    /**
     * Record usage of coupon by a user.
     *
     * @param int $userId
     * @param Model $model
     * @return bool
     */
    public function recordUsageByUser(int $userId, Model $model): bool
    {
        // أولا نتحقق إذا كان المستخدم قد استخدم هذا الكوبون سابقا على هذا النوع من النماذج
        if (UserCouponUsage::hasUserUsedCoupon($userId, $this->id, get_class($model))) {
            return false;
        }

        // نسجل استخدام الكوبون
        UserCouponUsage::recordUsage($userId, $this->id, $model);

        // نزيد عداد الاستخدام الإجمالي
        return $this->incrementUsage();
    }

    /**
     * Calculate discount amount for a given subtotal.
     *
     * @param float $subtotal
     * @return float
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_order_amount) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return round($subtotal * ($this->value / 100), 2);
        }

        return min($this->value, $subtotal);
    }

    /**
     * Check if the coupon applies to a specific product.
     *
     * @param int $productId
     * @return bool
     */
    public function appliesTo(int $productId): bool
    {
        if (!$this->applies_to_products) {
            return false;
        }

        if ($this->applies_to_all_products) {
            return true;
        }

        return $this->products()->where('products.id', $productId)->exists();
    }

    /**
     * Check if the coupon applies to a specific package.
     *
     * @param int $packageId
     * @return bool
     */
    public function appliesToPackage(int $packageId): bool
    {
        if (!$this->applies_to_packages) {
            return false;
        }

        return $this->packages()->where('packages.id', $packageId)->exists();
    }

    /**
     * Increment the used count of the coupon.
     *
     * @return bool
     */
    public function incrementUsage(): bool
    {
        $this->used_count++;
        return $this->save();
    }
}
