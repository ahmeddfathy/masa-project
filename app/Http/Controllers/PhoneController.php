<?php

namespace App\Http\Controllers;

use App\Models\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^(05)[0-9]{8}$/', // Saudi mobile number format
                'unique:phone_numbers,phone,NULL,id,user_id,' . Auth::id()
            ],
            'type' => 'required|in:' . implode(',', array_keys(PhoneNumber::TYPES))
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يكون رقم سعودي صحيح يبدأ ب 05 ويتكون من 10 أرقام',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً'
        ]);

        $phone = new PhoneNumber();
        $phone->user_id = Auth::id();
        $phone->phone = $request->phone;
        $phone->type = $request->type;
        $phone->save();

        return response()->json(['message' => 'تم إضافة رقم الهاتف بنجاح']);
    }

    public function show($id)
    {
        $phone = PhoneNumber::where('user_id', Auth::id())->findOrFail($id);
        return response()->json([
            'id' => $phone->id,
            'phone' => $phone->phone,
            'type' => $phone->type,
            'type_text' => $phone->type_text
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^(05)[0-9]{8}$/', // Saudi mobile number format
                'unique:phone_numbers,phone,' . $id . ',id,user_id,' . Auth::id()
            ],
            'type' => 'required|string|in:' . implode(',', array_keys(PhoneNumber::TYPES))
        ], [
            'phone.regex' => 'رقم الهاتف يجب أن يكون رقم سعودي صحيح يبدأ ب 05 ويتكون من 10 أرقام',
            'phone.unique' => 'رقم الهاتف مسجل مسبقاً',
            'type.in' => 'نوع الهاتف غير صالح'
        ]);

        $phone = PhoneNumber::where('user_id', Auth::id())->findOrFail($id);
        $phone->phone = $request->phone;
        $phone->type = $request->type;
        $phone->save();

        return response()->json([
            'message' => 'تم تحديث رقم الهاتف بنجاح',
            'phone' => [
                'id' => $phone->id,
                'phone' => $this->formatPhoneNumber($phone->phone),
                'type' => $phone->type,
                'type_text' => $phone->type_text,
                'is_primary' => $phone->is_primary
            ]
        ]);
    }

    public function destroy($id)
    {
        $phone = PhoneNumber::where('user_id', Auth::id())->findOrFail($id);

        // إذا كان هذا الرقم رئيسياً، نقوم بتعيين رقم آخر كرقم رئيسي
        if ($phone->is_primary) {
            $newPrimary = PhoneNumber::where('user_id', Auth::id())
                ->where('id', '!=', $id)
                ->first();

            if ($newPrimary) {
                $newPrimary->setAsPrimary();
            }
        }

        // حذف نهائي للرقم
        $phone->forceDelete(); // not needed anymore since we removed SoftDeletes, but kept for clarity

        return response()->json(['message' => 'تم حذف رقم الهاتف بنجاح']);
    }

    public function makePrimary(PhoneNumber $phone)
    {
        // التأكد من أن الرقم يخص المستخدم الحالي
        if ($phone->user_id !== auth()->id()) {
            return response()->json(['message' => 'غير مصرح لك بهذا الإجراء'], 403);
        }

        // إلغاء تعيين الرقم الرئيسي السابق
        PhoneNumber::where('user_id', auth()->id())
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        // تعيين الرقم الجديد كرقم رئيسي
        $phone->update(['is_primary' => true]);

        return response()->json(['message' => 'تم تعيين الرقم كرقم رئيسي بنجاح']);
    }

    private function formatPhoneNumber(string $phone): string
    {
        return substr($phone, 0, 2) . ' ' . substr($phone, 2, 3) . ' ' . substr($phone, 5);
    }
}
