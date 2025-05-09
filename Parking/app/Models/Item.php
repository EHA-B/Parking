<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'item',
        'quantity',
        'price'
];
public function services()  
{  
    return $this->belongsToMany(Service::class, 'vic_service')->withPivot('id' ,'service_id' ,'item_id' ,'parking_slot_id');;  
}  
}
