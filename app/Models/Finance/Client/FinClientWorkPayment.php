<?php

namespace App\Models\Finance\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinClientWorkPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'amount', 'amount_currency', 'fin_client_work_price_id'
    ];

    public function price(): BelongsTo
    {
        return $this->belongsTo(FinClientWorkPrice::class);
    }
}
