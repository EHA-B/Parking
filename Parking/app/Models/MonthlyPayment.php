<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_slot_id',
        'amount',
        'remaining_amount',
        'payment_status',
        'payment_date',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2'
    ];

    public function parkingSlot()
    {
        return $this->belongsTo(ParkingSlot::class);
    }
} 