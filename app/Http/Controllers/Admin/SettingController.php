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
            'max_concurrent_bookings' => Setting::get('max_concurrent_bookings', 1)
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'max_concurrent_bookings' => 'required|integer|min:1'
        ]);

        Setting::where('key', 'max_concurrent_bookings')
            ->update(['value' => $validated['max_concurrent_bookings']]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'تم تحديث إعدادات الحجز بنجاح');
    }
}
