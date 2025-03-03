<?php

namespace App\Models\Finance\Subscriber;

use App\Models\Subscriber\Subscriber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinSubscriberPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'date_end',
        'price',
        'status',
        'subscriber_id'
    ];



    public function payments(): HasMany
    {
        return $this->hasMany(FinSubscriberPayment::class);
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
}
