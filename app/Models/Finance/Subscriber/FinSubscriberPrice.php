<?php

namespace App\Models\Finance\Subscriber;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinSubscriberPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'date_end', 'price', 'client_work_id'
    ];
}
