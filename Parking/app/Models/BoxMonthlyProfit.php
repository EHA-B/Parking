<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxMonthlyProfit extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'profit',
        'calculated_at',
    ]; 
}
