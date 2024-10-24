<?php

namespace App\Models\Subscriber;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriberWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'what_done', 'result', 'subscriber_id'
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
}
