<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Handle file upload and return the file path
     *
     * @param UploadedFile $file
     * @param string $path
     * @return string
     */
    protected function uploadFile(UploadedFile $file, string $path): string
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        return $file->storeAs($path, $fileName, 'public');
    }

    /**
     * Delete file from storage
     *
     * @param string $path
     * @return bool
     */
    protected function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * Format price to store in database (converts to cents)
     *
     * @param float $price
     * @return int
     */


    /**
     * Format price for display (converts from cents to dollars)
     *
     * @param int $price
     * @return float
     */

}
