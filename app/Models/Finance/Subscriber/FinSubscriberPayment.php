<?php

namespace App\Models\Finance\Subscriber;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinSubscriberPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'amount', 'price_id'
    ];
}
