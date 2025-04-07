<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  

class ParkingSlot extends Model  
{  
    use HasFactory;  

    protected $fillable = ['slot_id', 'vic_id', 'parcode', 'time_in', 'time_out'];  

    public function vic()  
    {  
        return $this->belongsTo(Vic::class);  
    }  

    public function slot()  
    {  
        return $this->belongsTo(Slot::class, 'slot_id');  
    }  
}  