<?php

namespace App\Models\Finance\Subscriber;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinSubscriberPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'amount', 'amount_currency', 'fin_subscriber_price_id'
    ];

    public function price(): BelongsTo
    {
        return $this->belongsTo(FinSubscriberPrice::class);
    }
}
