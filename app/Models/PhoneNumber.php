<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'type',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    /**
     * القيم المسموح بها لنوع رقم الهاتف
     */
    const TYPES = [
        'mobile' => 'جوال',
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
     * الحصول على النص العربي لنوع رقم الهاتف
     */
    public function getTypeTextAttribute()
    {
        return self::TYPES[$this->type] ?? 'غير محدد';
    }

    /**
     * تعيين الرقم كرقم رئيسي
     */
    public function setAsPrimary()
    {
        // إلغاء تعيين الرقم الرئيسي السابق
        static::where('user_id', $this->user_id)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        // تعيين هذا الرقم كرقم رئيسي
        $this->is_primary = true;
        $this->save();
    }
}
