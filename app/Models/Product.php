<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
  use HasFactory, Searchable;

  protected $fillable = [
    'name',
    'slug',
    'description',
    'price',
    'stock',
    'is_available',
    'category_id'
  ];

  protected $casts = [
    'stock' => 'integer',
    'is_available' => 'boolean'
  ];

  protected $searchableFields = [
    'name',
    'description',
    'sku'
  ];

  protected $filterableFields = [
    'category_id',
    'price',
    'stock',
    'featured'
  ];

  protected $appends = [
    'image_url',
    'all_images'
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($product) {
      $product->slug = Str::slug($product->name);

      // Ensure unique slug
      $count = 1;
      while (static::where('slug', $product->slug)->exists()) {
        $product->slug = Str::slug($product->name) . '-' . $count;
        $count++;
      }
    });
  }

  public function getRouteKeyName()
  {
    return 'slug';
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  public function images(): HasMany
  {
    return $this->hasMany(ProductImage::class);
  }

  public function colors(): HasMany
  {
    return $this->hasMany(ProductColor::class);
  }

  public function sizes(): HasMany
  {
    return $this->hasMany(ProductSize::class);
  }

  public function orderItems(): HasMany
  {
    return $this->hasMany(OrderItem::class);
  }

  public function scopePriceRange(Builder $query, $min = null, $max = null): Builder
  {
    if ($min !== null) {
      $query->where('price', '>=', $min);
    }

    if ($max !== null) {
      $query->where('price', '<=', $max);
    }

    return $query;
  }

  public function scopeFeatured(Builder $query): Builder
  {
    return $query->where('featured', true);
  }

  public function scopeInStock(Builder $query): Builder
  {
    return $query->where('stock', '>', 0);
  }

  public function getPrimaryImageAttribute()
  {
    return $this->images->where('is_primary', true)->first()
      ?? $this->images->first();
  }

  public function getImageUrlAttribute()
  {
    if ($image = $this->primary_image) {
      return Storage::url($image->image_path);
    }
    return asset('images/placeholder.jpg');
  }

  public function getAllImagesAttribute()
  {
    return $this->images->map(function($image) {
      return Storage::url($image->image_path);
    })->toArray();
  }

  public function toArray()
  {
    $array = parent::toArray();
    $array['category_name'] = $this->category->name ?? null;
    return $array;
  }
}
