<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
  public function index()
  {
    $this->authorize('viewAny', Category::class);
    $categories = Category::withCount('products')->get();
    return view('admin.categories.index', compact('categories'));
  }

  public function create()
  {
    $this->authorize('create', Category::class);
    return view('admin.categories.create');
  }

  public function store(Request $request)
  {
    $this->authorize('create', Category::class);

    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:categories',
      'description' => 'nullable|string'
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    Category::create($validated);

    return redirect()->route('admin.categories.index')
      ->with('success', 'Category created successfully.');
  }

  public function edit(Category $category)
  {
    $this->authorize('update', $category);
    return view('admin.categories.edit', compact('category'));
  }

  public function update(Request $request, Category $category)
  {
    $this->authorize('update', $category);

    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
      'description' => 'nullable|string'
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    $category->update($validated);

    return redirect()->route('admin.categories.index')
      ->with('success', 'Category updated successfully.');
  }

  public function destroy(Category $category)
  {
    $this->authorize('delete', $category);

    if ($category->products()->count() > 0) {
      return back()->with('error', 'Cannot delete category with associated products.');
    }

    $category->delete();

    return redirect()->route('admin.categories.index')
      ->with('success', 'Category deleted successfully.');
  }
}
