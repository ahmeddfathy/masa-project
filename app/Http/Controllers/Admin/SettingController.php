<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'max_concurrent_bookings' => Setting::get('max_concurrent_bookings', 1),
            'show_store_appointments' => Setting::getBool('show_store_appointments', true)
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_concurrent_bookings' => 'required|integer|min:1',
            'show_store_appointments' => 'nullable|boolean'
        ]);

        Setting::where('key', 'max_concurrent_bookings')
            ->update(['value' => $validated['max_concurrent_bookings']]);

        Setting::where('key', 'show_store_appointments')
            ->update(['value' => isset($validated['show_store_appointments']) ? 'true' : 'false']);

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
