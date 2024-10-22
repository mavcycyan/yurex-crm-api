<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_date', 'visit_purpose', 'what_done', 'result', 'fee', 'client_id'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
