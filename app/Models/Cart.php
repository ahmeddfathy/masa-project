<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'session_id',
    'total_amount',
  ];

  protected $casts = [
    'total_amount' => 'integer',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function items(): HasMany
  {
    return $this->hasMany(CartItem::class);
  }

  public function updateTotals()
  {
    $this->total_amount = $this->items->sum('subtotal');
    $this->save();
  }
}
