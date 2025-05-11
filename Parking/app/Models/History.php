<?php
namespace App\Models;  

use Illuminate\Database\Eloquent\Factories\HasFactory;  
use Illuminate\Database\Eloquent\Model;  

class History extends Model  
{  
    use HasFactory;  

    protected $fillable = ['customer_name', 'vic_typ', 'vic_plate', 'time_in', 'time_out', 'price','services','duration' ,'notes','parking_type'];  
}  