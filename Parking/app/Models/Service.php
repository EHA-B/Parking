<?php
namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  

class Service extends Model  
{  
    use HasFactory;  

    protected $fillable = ['name', 'cost'];  

    public function vics()  
    {  
        return $this->belongsToMany(Vic::class, 'vic_service')->withPivot('id' ,'service_id' ,'vic_id' ,'parking_slot_id');  
    }  
}  