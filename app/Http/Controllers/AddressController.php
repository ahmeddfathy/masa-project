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
            'city' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'building_no' => 'nullable|string|max:50',
            'details' => 'nullable|string|max:500',
            'type' => 'required|string'
        ]);

        $address = new Address();
        $address->user_id = Auth::id();
        $address->type = $request->type;
        $address->city = $request->city;
        $address->area = $request->area;
        $address->street = $request->street;
        $address->building_no = $request->building_no;
        $address->details = $request->details;

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
            'city' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'building_no' => 'nullable|string|max:50',
            'details' => 'nullable|string|max:500',
            'type' => 'required|string'
        ]);

        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->type = $request->type;
        $address->city = $request->city;
        $address->area = $request->area;
        $address->street = $request->street;
        $address->building_no = $request->building_no;
        $address->details = $request->details;
        $address->save();

        return response()->json([
            'message' => 'تم تحديث العنوان بنجاح',
            'address' => [
                'id' => $address->id,
                'full_address' => $address->full_address,
                'type' => $address->type,
                'is_primary' => $address->is_primary
            ]
        ]);
    }

    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        if ($address->is_primary) {
            $newPrimary = Address::where('user_id', Auth::id())
                ->where('id', '!=', $id)
                ->first();

            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        $address->delete();

        return response()->json(['message' => 'تم حذف العنوان بنجاح']);
    }

    public function makePrimary($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        Address::where('user_id', Auth::id())
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        $address->update(['is_primary' => true]);

        return response()->json(['message' => 'تم تعيين العنوان كعنوان رئيسي بنجاح']);
    }
}
