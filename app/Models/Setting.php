<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'description'];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function getBool($key, $default = false)
    {
        $value = static::get($key, $default ? 'true' : 'false');
        return $value === 'true' || $value === '1' || $value === true;
    }
}
