<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'closing_balance',
        'closed_at',
    ];
}
