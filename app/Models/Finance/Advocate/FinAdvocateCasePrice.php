<?php

namespace App\Models\Finance\Advocate;

use App\Models\Advocate\AdvocateCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinAdvocateCasePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'date_end',
        'price',
        'name',
        'deadlines',
        'status',
        'advocate_case_id'
    ];




    public function payments(): HasMany
    {
        return $this->hasMany(FinAdvocateCasePayment::class);
    }

    public function advocate_case(): BelongsTo
    {
        return $this->belongsTo(AdvocateCase::class);
    }
}
