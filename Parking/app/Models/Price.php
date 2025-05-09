<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_hourly_rate',
        'car_daily_rate',
        'car_monthly_rate',
        'moto_hourly_rate',
        'moto_daily_rate',
        'moto_monthly_rate'
    ];
}