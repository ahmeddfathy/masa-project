<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * عرض صفحة إعدادات النظام
     */
    public function index()
    {
        // جلب الإعدادات الحالية
        $settings = [
            'max_concurrent_bookings' => Setting::get('max_concurrent_bookings', 1),
            'studio_start_time' => Setting::get('studio_start_time', '10:00'),
            'studio_end_time' => Setting::get('studio_end_time', '18:00'),
            'show_store_appointments' => Setting::get('show_store_appointments', false),
        ];

        // تسجيل القيم المستخدمة للمساعدة في التشخيص
        \Log::debug('Current settings values', $settings);

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * تحديث إعدادات النظام
     */
    public function update(Request $request)
    {
        // إزالة قاعدة after: من التحقق ليسمح بأوقات تمر عبر منتصف الليل
        $validated = $request->validate([
            'max_concurrent_bookings' => 'required|integer|min:1',
            'studio_start_time' => 'required|date_format:H:i',
            'studio_end_time' => 'required|date_format:H:i',
            'show_store_appointments' => 'nullable|boolean',
        ]);

        // تنظيف وتنسيق الأوقات قبل الحفظ
        $startTime = Carbon::createFromFormat('H:i', $validated['studio_start_time'])->format('H:i');
        $endTime = Carbon::createFromFormat('H:i', $validated['studio_end_time'])->format('H:i');

        // حفظ الإعدادات
        Setting::set('max_concurrent_bookings', $validated['max_concurrent_bookings']);
        Setting::set('studio_start_time', $startTime);
        Setting::set('studio_end_time', $endTime);
        Setting::set('show_store_appointments', $request->has('show_store_appointments') ? 1 : 0);

        // السجل الجديد
        \Log::info('Settings updated for overnight schedule', [
            'max_concurrent_bookings' => Setting::get('max_concurrent_bookings'),
            'studio_start_time' => Setting::get('studio_start_time'),
            'studio_end_time' => Setting::get('studio_end_time'),
        ]);

        // تنظيف الكاش لضمان استخدام القيم الجديدة
        Artisan::call('cache:clear');

        return redirect()->back()->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
