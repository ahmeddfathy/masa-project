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
            'phone' => 'required|string|max:20|unique:phone_numbers,phone,NULL,id,user_id,' . Auth::id(),
            'type' => 'required|string'
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
            'type' => $phone->type
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'phone' => 'required|string|max:20|unique:phone_numbers,phone,' . $id . ',id,user_id,' . Auth::id(),
            'type' => 'required|string'
        ]);

        $phone = PhoneNumber::where('user_id', Auth::id())->findOrFail($id);
        $phone->phone = $request->phone;
        $phone->type = $request->type;
        $phone->save();

        return response()->json([
            'message' => 'تم تحديث رقم الهاتف بنجاح',
            'phone' => [
                'id' => $phone->id,
                'phone' => $phone->phone,
                'type' => $phone->type,
                'is_primary' => $phone->is_primary
            ]
        ]);
    }

    public function destroy($id)
    {
        $phone = PhoneNumber::where('user_id', Auth::id())->findOrFail($id);

        if ($phone->is_primary) {
            $newPrimary = PhoneNumber::where('user_id', Auth::id())
                ->where('id', '!=', $id)
                ->first();

            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        $phone->delete();

        return response()->json(['message' => 'تم حذف رقم الهاتف بنجاح']);
    }

    public function makePrimary($id)
    {
        $phone = PhoneNumber::where('user_id', Auth::id())->findOrFail($id);

        PhoneNumber::where('user_id', Auth::id())
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        $phone->update(['is_primary' => true]);

        return response()->json(['message' => 'تم تعيين الرقم كرقم رئيسي بنجاح']);
    }

    private function formatPhoneNumber(string $phone): string
    {
        return substr($phone, 0, 2) . ' ' . substr($phone, 2, 3) . ' ' . substr($phone, 5);
    }
}
