<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentBox extends Model
{
    use HasFactory;

    protected $table = 'current_box';
    public $timestamps = false;
    protected $fillable = ['current_balance'];
} 