<?php

namespace App\Models\Advocate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdvocateCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'parent_id'
    ];

    public function advocate_cases(): HasMany
    {
        return $this->hasMany(AdvocateCase::class);
    }

    public function children()
    {
        return $this->hasMany(AdvocateCategory::class, 'parent_id');
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}
