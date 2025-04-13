<?php
namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  


class Vic extends Model  
{  
    use HasFactory;  

    protected $fillable = ['brand', 'typ', 'plate', 'customer_id'];  

    // Add eager loading for items through vic_service
    protected $with = ['items'];

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
        return $this->belongsToMany(Service::class, 'vic_service')->withPivot('id' ,'service_id' ,'vic_id' ,'parking_slot_id');  
    }  

    // New method to get associated items
    public function items()  
    {  
        return $this->belongsToMany(Item::class, 'vic_service', 'vic_id', 'item_id')
            ->withPivot('item_quantity', 'parking_slot_id')
            ->wherePivotNotNull('item_id');
    }
}  