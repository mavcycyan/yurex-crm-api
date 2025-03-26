<?php

namespace App\Models\Finance\Client;

use App\Models\Client\ClientWork;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinClientWorkPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'date_end',
        'price',
        'price_cur',
        'status',
        'client_work_id'
    ];



    public function payments(): HasMany
    {
        return $this->hasMany(FinClientWorkPayment::class);
    }

    public function client_work(): BelongsTo
    {
        return $this->belongsTo(ClientWork::class);
    }
}
