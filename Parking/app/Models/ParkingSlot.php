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
        'parking_type', // 'hourly', 'daily', or 'monthly'
        'status' // 'in' or 'out'
    ];

    public function vics()
    {
        return $this->belongsTo(Vic::class, 'vic_id');
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class, 'slot_id');
    }

    public function monthlyPayments()
    {
        return $this->hasMany(MonthlyPayment::class);
    }

    public function getTotalPaidAmount()
    {
        return $this->monthlyPayments()->sum('amount');
    }

    public function getRemainingAmount()
    {
        if ($this->parking_type !== 'monthly') {
            return 0;
        }
        
        $totalAmount = $this->price;
        $paidAmount = $this->getTotalPaidAmount();
        return max(0, $totalAmount - $paidAmount);
    }
}