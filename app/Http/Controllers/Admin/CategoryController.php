<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
  public function index()
  {
    $categories = Category::withCount('products')
      ->latest()
      ->paginate(10);

    return view('admin.categories.index', compact('categories'));
  }

  public function create()
  {
    return view('admin.categories.create');
  }

  public function show(Category $category)
  {
    $category->loadCount('products');
    $category->load('products');

    return view('admin.categories.show', compact('category'));
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:categories',
      'description' => 'nullable|string'
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    Category::create($validated);

    return redirect()->route('admin.categories.index')
      ->with('success', 'تم إضافة التصنيف بنجاح');
  }

  public function edit(Category $category)
  {
    return view('admin.categories.edit', compact('category'));
  }

  public function update(Request $request, Category $category)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
      'description' => 'nullable|string'
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    $category->update($validated);

    return redirect()->route('admin.categories.index')
      ->with('success', 'تم تحديث التصنيف بنجاح');
  }

  public function destroy(Category $category)
  {
    if ($category->products()->exists()) {
      return back()->with('error', 'لا يمكن حذف التصنيف لوجود منتجات مرتبطة به');
    }

    $category->delete();

    return redirect()->route('admin.categories.index')
      ->with('success', 'تم حذف التصنيف بنجاح');
  }
}
