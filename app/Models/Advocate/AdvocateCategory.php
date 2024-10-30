<?php

namespace App\Models\Advocate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvocateCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug'
    ];

    public function advocate_cases(): HasMany
    {
        return $this->hasMany(AdvocateCase::class);
    }
}
