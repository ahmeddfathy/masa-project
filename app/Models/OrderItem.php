<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'order_id',
    'product_id',
    'appointment_id',
    'quantity',
    'unit_price',
    'subtotal',
    'color',
    'size'
  ];

  protected $casts = [
    'unit_price' => 'integer',
    'subtotal' => 'integer'
  ];

  public function order(): BelongsTo
  {
    return $this->belongsTo(Order::class);
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  public function appointment(): BelongsTo
  {
    return $this->belongsTo(Appointment::class);
  }
}
