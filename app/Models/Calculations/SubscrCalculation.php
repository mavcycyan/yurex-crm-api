<?php

namespace App\Models\Calculations;

use App\Models\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscrCalculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscriber_id',
        'chalk',
        'payments',
        'date',
    ];
}
