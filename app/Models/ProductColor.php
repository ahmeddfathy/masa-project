<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductColor extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
