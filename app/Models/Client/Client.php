<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'where_knows', 'birthday', 'address', 'phone', 'worker', 'review', 'inactive'
    ];

    public function client_works(): HasMany
    {
        return $this->hasMany(ClientWork::class);
    }

    public function oldestWork()
    {
        return $this->hasOne(ClientWork::class, 'client_id')->oldest('visit_date');
    }
}
