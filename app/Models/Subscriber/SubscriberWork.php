<?php

namespace App\Models\Subscriber;

use App\Models\Finance\Client\FinClientWorkPrice;
use App\Models\Finance\Subscriber\FinSubscrWorkPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriberWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'date_end',
        'name',
        'what_done',
        'result',
        'worker',
        'subscriber_id'
    ];

    protected $casts = [
        'what_done' => 'array',
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function latestFinSubscrWorkPrice()
    {
        return $this->hasOne(FinSubscrWorkPrice::class, 'subscriber_work_id')->latest('created_at');
    }
}
