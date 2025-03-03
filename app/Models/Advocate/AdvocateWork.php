<?php

namespace App\Models\Advocate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvocateWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'date_end',
        'name',
        'what_done',
        'result',
        'worker',
        'advocate_case_id'
    ];

    protected $casts = [
        'what_done' => 'array',
    ];

    public function advocate_case(): BelongsTo
    {
        return $this->belongsTo(AdvocateCase::class);
    }
}
