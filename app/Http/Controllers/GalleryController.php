<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        // Get all gallery images grouped by category
        $images = Gallery::all()->groupBy('category');

        // Get unique categories for filter buttons
        $categories = Gallery::distinct()->pluck('category');

        return view('gallery', compact('images', 'categories'));
    }
}
