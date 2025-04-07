<?php

namespace App\Models\Finance\Subscriber;

use App\Models\Subscriber\SubscriberWork;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinSubscrWorkPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'date_end',
        'price',
        'price_cur',
        'name',
        'deadlines',
        'status',
        'info',
        'subscriber_work_id'
    ];



    public function payments(): HasMany
    {
        return $this->hasMany(FinSubscrWorkPayment::class);
    }

    public function subscriber_work(): BelongsTo
    {
        return $this->belongsTo(SubscriberWork::class);
    }
}
