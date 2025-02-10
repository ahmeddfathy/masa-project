<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'city' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s]+$/u'
            ],
            'area' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s\d]+$/u'
            ],
            'street' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s\d]+$/u'
            ],
            'building_no' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\p{Arabic}\s\d-]+$/u'
            ],
            'details' => 'nullable|string|max:500',
            'type' => 'required|string|in:' . implode(',', array_keys(Address::TYPES))
        ], [
            'city.regex' => 'يجب أن يحتوي اسم المدينة على حروف عربية فقط',
            'area.regex' => 'يجب أن يحتوي اسم المنطقة على حروف عربية وأرقام فقط',
            'street.regex' => 'يجب أن يحتوي اسم الشارع على حروف عربية وأرقام فقط',
            'building_no.regex' => 'رقم المبنى يجب أن يحتوي على أرقام وحروف عربية فقط',
            'type.in' => 'نوع العنوان غير صالح'
        ]);

        $address = new Address();
        $address->user_id = Auth::id();
        $address->type = $request->type;
        $address->city = $request->city;
        $address->area = $request->area;
        $address->street = $request->street;
        $address->building_no = $request->building_no;
        $address->details = $request->details;

        // إذا كان هذا أول عنوان للمستخدم، نجعله العنوان الرئيسي
        if (!Address::where('user_id', Auth::id())->exists()) {
            $address->is_primary = true;
        }

        $address->save();

        return response()->json([
            'message' => 'تم إضافة العنوان بنجاح',
            'address' => [
                'id' => $address->id,
                'full_address' => $address->full_address,
                'type' => $address->type,
                'type_text' => $address->type_text,
                'is_primary' => $address->is_primary
            ]
        ]);
    }

    public function show($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($address);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'city' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s]+$/u'
            ],
            'area' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s\d]+$/u'
            ],
            'street' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\s\d]+$/u'
            ],
            'building_no' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[\p{Arabic}\s\d-]+$/u'
            ],
            'details' => 'nullable|string|max:500',
            'type' => 'required|in:' . implode(',', array_keys(Address::TYPES))
        ], [
            'city.regex' => 'يجب أن يحتوي اسم المدينة على حروف عربية فقط',
            'area.regex' => 'يجب أن يحتوي اسم المنطقة على حروف عربية وأرقام فقط',
            'street.regex' => 'يجب أن يحتوي اسم الشارع على حروف عربية وأرقام فقط',
            'building_no.regex' => 'رقم المبنى يجب أن يحتوي على أرقام وحروف عربية فقط'
        ]);

        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->type = $request->type;
        $address->city = $request->city;
        $address->area = $request->area;
        $address->street = $request->street;
        $address->building_no = $request->building_no;
        $address->details = $request->details;
        $address->save();

        return response()->json(['message' => 'تم تحديث العنوان بنجاح']);
    }

    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        if ($address->is_primary) {
            $newPrimary = Address::where('user_id', Auth::id())
                ->where('id', '!=', $id)
                ->first();

            if ($newPrimary) {
                $newPrimary->setAsPrimary();
            }
        }

        // حذف نهائي للعنوان
        $address->forceDelete(); // not needed anymore since we removed SoftDeletes, but kept for clarity

        return response()->json(['message' => 'تم حذف العنوان بنجاح']);
    }

    public function makePrimary(Address $address)
    {
        // التأكد من أن العنوان يخص المستخدم الحالي
        if ($address->user_id !== auth()->id()) {
            return response()->json(['message' => 'غير مصرح لك بهذا الإجراء'], 403);
        }

        // إلغاء تعيين العنوان الرئيسي السابق
        Address::where('user_id', auth()->id())
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        // تعيين العنوان الجديد كعنوان رئيسي
        $address->update(['is_primary' => true]);

        return response()->json(['message' => 'تم تعيين العنوان كعنوان رئيسي بنجاح']);
    }
}
