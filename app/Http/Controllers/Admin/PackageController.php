<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('services')->latest()->paginate(10);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->get();
        return view('admin.packages.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'num_photos' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id'
        ]);

        $package = Package::create($validated);
        $package->services()->attach($validated['service_ids']);

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم إضافة الباقة بنجاح');
    }

    public function edit(Package $package)
    {
        $services = Service::where('is_active', true)->get();
        return view('admin.packages.edit', compact('package', 'services'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'num_photos' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id'
        ]);

        $package->update($validated);
        $package->services()->sync($validated['service_ids']);

        return redirect()->route('admin.packages.index')
            ->with('success', 'تم تحديث الباقة بنجاح');
    }

    public function destroy(Package $package)
    {
        $package->delete();
        return redirect()->route('admin.packages.index')
            ->with('success', 'تم حذف الباقة بنجاح');
    }
}
