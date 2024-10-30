<?php

namespace App\Models\Advocate;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvocateCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_date', 'advocates', 'proc_num', 'plot', 'fee', 'result', 'advocate_category_id', 'advocate_id'
    ];

    protected $casts = [
        'advocates' => 'array',
    ];

    public function advocate(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function advocate_category(): BelongsTo
    {
        return $this->belongsTo(AdvocateCategory::class);
    }

    public function advocate_works(): HasMany
    {
        return $this->hasMany(AdvocateWork::class);
    }
}
