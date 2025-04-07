<?php
namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  

class Slot extends Model  
{  
    use HasFactory;  

    protected $fillable = ['status'];  

    public function vics()  
    {  
        return $this->belongsToMany(Vic::class, 'parking_slots')->withPivot('id' ,'slot_id' ,'vic_id' ,'parcode' ,'time_in' ,'time_out' );  
    }  
}  
