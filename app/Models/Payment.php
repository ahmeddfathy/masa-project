<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'response_data'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'response_data' => 'array'
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
