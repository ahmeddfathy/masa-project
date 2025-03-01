<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    public static function get($key, $default = null)
    {
        // استخدام الكاش لتحسين الأداء
        return Cache::remember('setting_' . $key, 60, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set($key, $value)
    {
        // حذف الكاش القديم
        Cache::forget('setting_' . $key);

        // تحديث أو إنشاء الإعداد
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getBool($key, $default = false)
    {
        $value = static::get($key, $default ? 'true' : 'false');
        return $value === 'true' || $value === '1' || $value === true;
    }
}
