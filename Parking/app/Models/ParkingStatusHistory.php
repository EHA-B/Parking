<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_slot_id',
        'customer_id',
        'status',
        'changed_at'
    ];

    protected $casts = [
        'changed_at' => 'datetime'
    ];

    public function parkingSlot()
    {
        return $this->belongsTo(ParkingSlot::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}