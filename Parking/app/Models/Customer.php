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
}  