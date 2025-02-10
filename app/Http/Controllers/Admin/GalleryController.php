<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $images = Gallery::latest()->paginate(12);
        return view('admin.gallery.index', compact('images'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'caption' => 'required|string|max:255',
            'category' => 'required|string|max:100'
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        Gallery::create([
            'image_url' => $path,
            'caption' => $validated['caption'],
            'category' => $validated['category']
        ]);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'تم إضافة الصورة بنجاح');
    }

    public function show(Gallery $gallery)
    {
        return view('admin.gallery.show', compact('gallery'));
    }

    public function edit(Gallery $gallery)
    {
        return view('admin.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, Gallery $gallery)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'caption' => 'required|string|max:255',
            'category' => 'required|string|max:100'
        ]);

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if (Storage::disk('public')->exists($gallery->image_url)) {
                Storage::disk('public')->delete($gallery->image_url);
            }
            // تخزين الصورة الجديدة
            $path = $request->file('image')->store('gallery', 'public');
            $gallery->image_url = $path;
        }

        $gallery->caption = $validated['caption'];
        $gallery->category = $validated['category'];
        $gallery->save();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'تم تحديث الصورة بنجاح');
    }

    public function destroy(Gallery $gallery)
    {
        // حذف الصورة من التخزين
        if (Storage::disk('public')->exists($gallery->image_url)) {
            Storage::disk('public')->delete($gallery->image_url);
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'تم حذف الصورة بنجاح');
    }
}
