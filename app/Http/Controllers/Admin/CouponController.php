<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Package;
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
        $packages = Package::with('services')->orderBy('name')->get();
        return view('admin.coupons.create', compact('products', 'packages'));
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
            'applies_to_products' => ['sometimes', 'boolean'],
            'applies_to_all_products' => ['sometimes', 'boolean'],
            'applies_to_packages' => ['sometimes', 'boolean'],
            'product_ids' => ['nullable', 'array', function ($attribute, $value, $fail) use ($request) {
                if ($request->boolean('applies_to_products') && !$request->boolean('applies_to_all_products') && empty($value)) {
                    $fail('يجب اختيار منتج واحد على الأقل عند تطبيق الكوبون على منتجات محددة.');
                }
            }],
            'product_ids.*' => ['nullable', 'exists:products,id'],
            'package_ids' => ['nullable', 'array', function ($attribute, $value, $fail) use ($request) {
                if ($request->boolean('applies_to_packages') && empty($value)) {
                    $fail('يجب اختيار باقة واحدة على الأقل عند تطبيق الكوبون على الباقات.');
                }
            }],
            'package_ids.*' => ['nullable', 'exists:packages,id'],
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
                    'is_active' => $request->boolean('is_active'),
                    'applies_to_products' => $request->boolean('applies_to_products'),
                    'applies_to_all_products' => $request->boolean('applies_to_all_products'),
                    'applies_to_packages' => $request->boolean('applies_to_packages'),
                    'starts_at' => $validated['starts_at'],
                    'expires_at' => $validated['expires_at'],
                ]);

                // If coupon applies to specific products, attach them
                if ($coupon->applies_to_products && !$coupon->applies_to_all_products && isset($validated['product_ids'])) {
                    $coupon->products()->attach($validated['product_ids']);
                }

                // If coupon applies to specific packages, attach them
                if ($coupon->applies_to_packages && isset($validated['package_ids'])) {
                    $coupon->packages()->attach($validated['package_ids']);
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
        $coupon->load(['products', 'packages.services']);
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit(Coupon $coupon)
    {
        $products = Product::orderBy('name')->get();
        $packages = Package::with('services')->orderBy('name')->get();
        $selectedProducts = $coupon->products->pluck('id')->toArray();
        $selectedPackages = $coupon->packages->pluck('id')->toArray();

        return view('admin.coupons.edit', compact(
            'coupon',
            'products',
            'packages',
            'selectedProducts',
            'selectedPackages'
        ));
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
            'applies_to_products' => ['sometimes', 'boolean'],
            'applies_to_all_products' => ['sometimes', 'boolean'],
            'applies_to_packages' => ['sometimes', 'boolean'],
            'product_ids' => ['nullable', 'array', function ($attribute, $value, $fail) use ($request) {
                if ($request->boolean('applies_to_products') && !$request->boolean('applies_to_all_products') && empty($value)) {
                    $fail('يجب اختيار منتج واحد على الأقل عند تطبيق الكوبون على منتجات محددة.');
                }
            }],
            'product_ids.*' => ['nullable', 'exists:products,id'],
            'package_ids' => ['nullable', 'array', function ($attribute, $value, $fail) use ($request) {
                if ($request->boolean('applies_to_packages') && empty($value)) {
                    $fail('يجب اختيار باقة واحدة على الأقل عند تطبيق الكوبون على الباقات.');
                }
            }],
            'package_ids.*' => ['nullable', 'exists:packages,id'],
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
                    'is_active' => $request->boolean('is_active'),
                    'applies_to_products' => $request->boolean('applies_to_products'),
                    'applies_to_all_products' => $request->boolean('applies_to_all_products'),
                    'applies_to_packages' => $request->boolean('applies_to_packages'),
                    'starts_at' => $validated['starts_at'],
                    'expires_at' => $validated['expires_at'],
                ]);

                // Sync products
                if ($coupon->applies_to_products && !$coupon->applies_to_all_products && isset($validated['product_ids'])) {
                    $coupon->products()->sync($validated['product_ids']);
                } else {
                    $coupon->products()->detach();
                }

                // Sync packages
                if ($coupon->applies_to_packages && isset($validated['package_ids'])) {
                    $coupon->packages()->sync($validated['package_ids']);
                } else {
                    $coupon->packages()->detach();
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
     * Toggle the status of a coupon.
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        return back()->with('success', 'تم تغيير حالة الكوبون بنجاح');
    }
}
