<?php

namespace App\Services\Booking;

use App\Models\Gallery;
use Illuminate\Support\Collection;

class GalleryService
{
    public function getLatestImages(int $limit = 5): Collection
    {
        return Gallery::latest()->take($limit)->get();
    }
}
