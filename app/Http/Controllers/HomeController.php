<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get latest 3 gallery images
        $latestImages = Gallery::latest()->take(3)->get();

        // Get active services
        $services = Service::where('is_active', true)->get();

        return view('index', compact('latestImages', 'services'));
    }
}
