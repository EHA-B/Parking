<?php
namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  


class Vic extends Model  
{  
    use HasFactory;  

    protected $fillable = ['brand', 'typ', 'plate', 'customer_id'];  

    public function customer()  
    {  
        return $this->belongsTo(Customer::class);  
    }  

    public function parkingSlots()  
    {  
        return $this->hasMany(ParkingSlot::class, 'vic_id');  
    }  

    public function slots()  
    {  
        return $this->belongsToMany(Slot::class, 'parking_slots')->withPivot('id' ,'slot_id' ,'vic_id' ,'parcode' ,'time_in' ,'time_out' );  
    }  

    public function services()  
    {  
        return $this->belongsToMany(Service::class, 'vic_service')->withPivot('id' ,'service_id' ,'vic_id' ,'parking_slot_id');;  
    }  
}  