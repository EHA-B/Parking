<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'vic_id',
        'parcode',
        'time_in',
        'time_out',
        'notes',
        'parking_type' // 'hourly', 'daily', or 'monthly'
    ];

    public function vics()
    {
        return $this->belongsTo(Vic::class, 'vic_id');
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class, 'slot_id');
    }
}