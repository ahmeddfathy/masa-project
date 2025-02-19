<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CartItem extends Model
{
  use HasFactory;

  protected $fillable = [
    'cart_id',
    'product_id',
    'quantity',
    'unit_price',
    'subtotal',
    'needs_appointment',
    'color',
    'size',
    'appointment_id'
  ];

  protected $casts = [
    'unit_price' => 'integer',
    'subtotal' => 'integer',
    'quantity' => 'integer',
    'needs_appointment' => 'boolean'
  ];

  public function cart(): BelongsTo
  {
    return $this->belongsTo(Cart::class);
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }

  public function appointment()
  {
    return $this->hasOne(Appointment::class);
  }

  public function needsAppointment(): bool
  {
    return $this->needs_appointment && !$this->appointment()->exists();
  }
}
