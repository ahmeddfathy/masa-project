<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageAddon;
use Illuminate\Http\Request;

class PackageAddonController extends Controller
{
    public function index()
    {
        $addons = PackageAddon::with('package')->latest()->paginate(10);
        return view('admin.addons.index', compact('addons'));
    }

    public function create()
    {
        $packages = Package::where('is_active', true)->get();
        return view('admin.addons.create', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'package_id' => 'required|exists:packages,id'
        ]);

        PackageAddon::create($validated);

        return redirect()->route('admin.addons.index')
            ->with('success', 'تم إضافة الإضافة بنجاح');
    }

    public function edit(PackageAddon $addon)
    {
        $packages = Package::where('is_active', true)->get();
        return view('admin.addons.edit', compact('addon', 'packages'));
    }

    public function update(Request $request, PackageAddon $addon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'package_id' => 'required|exists:packages,id'
        ]);

        $addon->update($validated);

        return redirect()->route('admin.addons.index')
            ->with('success', 'تم تحديث الإضافة بنجاح');
    }

    public function destroy(PackageAddon $addon)
    {
        $addon->delete();
        return redirect()->route('admin.addons.index')
            ->with('success', 'تم حذف الإضافة بنجاح');
    }
}
