<?php

namespace App\Models\Finance\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinClientWorkPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'amount', 'price_id'
    ];
}
