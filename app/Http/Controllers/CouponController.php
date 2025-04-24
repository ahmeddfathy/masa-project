<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons.
     */
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.coupons.create', compact('products'));
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order_amount' => ['required', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
            'applies_to_all_products' => ['sometimes', 'boolean'],
            'product_ids' => ['nullable', 'array', 'required_if:applies_to_all_products,0'],
            'product_ids.*' => ['nullable', 'exists:products,id'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Create the coupon
                $coupon = Coupon::create([
                    'name' => $validated['name'],
                    'code' => Str::upper($validated['code']),
                    'description' => $validated['description'],
                    'type' => $validated['type'],
                    'value' => $validated['value'],
                    'min_order_amount' => $validated['min_order_amount'],
                    'max_uses' => $validated['max_uses'],
                    'is_active' => $request->boolean('is_active', true),
                    'applies_to_all_products' => $request->boolean('applies_to_all_products', true),
                    'starts_at' => $validated['starts_at'],
                    'expires_at' => $validated['expires_at'],
                ]);

                // If coupon applies to specific products, attach them
                if (!$coupon->applies_to_all_products && isset($validated['product_ids'])) {
                    $coupon->products()->attach($validated['product_ids']);
                }

                return redirect()->route('admin.coupons.index')
                    ->with('success', 'تم إنشاء الكوبون بنجاح');
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'حدث خطأ أثناء إنشاء الكوبون: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified coupon.
     */
    public function show(Coupon $coupon)
    {
        $coupon->load('products');
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit(Coupon $coupon)
    {
        $products = Product::orderBy('name')->get();
        $selectedProducts = $coupon->products->pluck('id')->toArray();
        return view('admin.coupons.edit', compact('coupon', 'products', 'selectedProducts'));
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order_amount' => ['required', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
            'applies_to_all_products' => ['sometimes', 'boolean'],
            'product_ids' => ['nullable', 'array', 'required_if:applies_to_all_products,0'],
            'product_ids.*' => ['nullable', 'exists:products,id'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        try {
            return DB::transaction(function () use ($request, $validated, $coupon) {
                // Update the coupon
                $coupon->update([
                    'name' => $validated['name'],
                    'code' => Str::upper($validated['code']),
                    'description' => $validated['description'],
                    'type' => $validated['type'],
                    'value' => $validated['value'],
                    'min_order_amount' => $validated['min_order_amount'],
                    'max_uses' => $validated['max_uses'],
                    'is_active' => $request->boolean('is_active', true),
                    'applies_to_all_products' => $request->boolean('applies_to_all_products', true),
                    'starts_at' => $validated['starts_at'],
                    'expires_at' => $validated['expires_at'],
                ]);

                // Sync products
                if (!$coupon->applies_to_all_products && isset($validated['product_ids'])) {
                    $coupon->products()->sync($validated['product_ids']);
                } else {
                    $coupon->products()->detach();
                }

                return redirect()->route('admin.coupons.index')
                    ->with('success', 'تم تحديث الكوبون بنجاح');
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث الكوبون: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $coupon->delete();
            return redirect()->route('admin.coupons.index')
                ->with('success', 'تم حذف الكوبون بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء حذف الكوبون: ' . $e->getMessage());
        }
    }

    /**
     * Validate a coupon code.
     */
    public function validateCoupon(Request $request)
    {
        $code = $request->input('code');
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'كود الكوبون غير صالح'
            ]);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'كود الكوبون غير صالح أو منتهي الصلاحية'
            ]);
        }

        return response()->json([
            'valid' => true,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'min_order_amount' => $coupon->min_order_amount,
                'applies_to_all_products' => $coupon->applies_to_all_products
            ],
            'message' => 'تم تطبيق الكوبون بنجاح'
        ]);
    }
}
