<?php
namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  

class VicService extends Model  
{  
    use HasFactory;  

    protected $fillable = ['service_id', 'vic_id','item_id', 'parking_slot_id'];  

    public function vic()  
    {  
        return $this->belongsTo(Vic::class);  
    }  

    public function item()  
    {  
        return $this->belongsTo(Item::class);  
    }  

    public function service()  
    {  
        return $this->belongsTo(Service::class);  
    }  

    public function parkingSlot()  
    {  
        return $this->belongsTo(ParkingSlot::class);  
    }  
}  