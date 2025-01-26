<?php

namespace App\Models\Finance\Advocate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinAdvocateCasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'amount', 'price_id'
    ];
}
