<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CheckStoreAppointmentsFeature
{
    /**
     * التحقق من تفعيل ميزة مواعيد المتجر
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // التحقق من إعداد ظهور مواعيد المتجر
        if (!Setting::getBool('show_store_appointments', true)) {
            // التحقق مما إذا كان المستخدم مسؤولاً أم عميلاً
            if (auth()->check() && auth()->user()->hasRole('admin')) {
                // إعادة توجيه المسؤول إلى لوحة التحكم مع رسالة مختلفة
                return redirect()->route('admin.dashboard')
                    ->with('warning', 'تم تعطيل قسم مواعيد المتجر. يمكنك تفعيله من إعدادات النظام.');
            }

            // إعادة توجيه العميل إلى الصفحة الرئيسية
            return redirect()->route('dashboard')
                ->with('error', 'ميزة مواعيد المتجر غير متاحة حالياً');
        }

        return $next($request);
    }
}
