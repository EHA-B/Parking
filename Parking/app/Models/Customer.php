<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'hours'];

    public function vics()
    {
        return $this->hasMany(Vic::class);
    }

    public function hasMonthlySubscription()
    {
        return $this->vics()
            ->whereHas('parkingSlots', function ($query) {
                $query->where('parking_type', 'monthly')
                    ->whereNull('time_out');
            })
            ->exists();
    }
}