<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'city',
        'area',
        'street',
        'building_no',
        'details',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    /**
     * القيم المسموح بها لنوع العنوان
     */
    const TYPES = [
        'home' => 'منزل',
        'work' => 'عمل',
        'other' => 'آخر'
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * الحصول على النص العربي لنوع العنوان
     */
    public function getTypeTextAttribute()
    {
        return self::TYPES[$this->type] ?? 'غير محدد';
    }

    /**
     * تعيين العنوان كعنوان رئيسي
     */
    public function setAsPrimary()
    {
        // إلغاء تعيين العنوان الرئيسي السابق
        static::where('user_id', $this->user_id)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        // تعيين هذا العنوان كعنوان رئيسي
        $this->is_primary = true;
        $this->save();
    }

    /**
     * الحصول على العنوان كاملاً
     */
    public function getFullAddressAttribute()
    {
        $parts = [
            $this->city,
            $this->area,
            'شارع ' . $this->street
        ];

        if ($this->building_no) {
            $parts[] = 'مبنى ' . $this->building_no;
        }

        if ($this->details) {
            $parts[] = $this->details;
        }

        return implode('، ', array_filter($parts));
    }
}
