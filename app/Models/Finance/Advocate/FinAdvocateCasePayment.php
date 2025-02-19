<?php

namespace App\Models\Finance\Advocate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinAdvocateCasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'amount', 'amount_currency', 'fin_advocate_case_price_id'
    ];

    public function price(): BelongsTo
    {
        return $this->belongsTo(FinAdvocateCasePrice::class);
    }
}
