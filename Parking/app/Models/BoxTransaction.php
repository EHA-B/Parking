<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'type', // 'income' or 'outcome'
        'customer_id',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
} 