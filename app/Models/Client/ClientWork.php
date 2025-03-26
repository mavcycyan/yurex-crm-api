<?php

namespace App\Models\Client;

use App\Models\Finance\Client\FinClientWorkPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_date',
        'visit_date_end',
        'visit_purpose',
        'what_done',
        'result',
        'worker',
        'client_id'
    ];

    protected $casts = [
        'what_done' => 'array',
        'worker' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function latestFinClientWorkPrice()
    {
        return $this->hasOne(FinClientWorkPrice::class, 'client_work_id')->latest('created_at');
    }
}
