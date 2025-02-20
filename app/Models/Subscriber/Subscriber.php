<?php

namespace App\Models\Subscriber;

use App\Models\Finance\Subscriber\FinSubscriberPrice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'edrpou', 'contract_date', 'responsible_person_id', 'contact_person_name', 'contact_person_phone', 'director_person_name', 'director_person_phone', 'legal_address', 'actual_address', 'info'
    ];

    public function subscriber_works(): HasMany
    {
        return $this->hasMany(SubscriberWork::class);
    }

    public function latestFinSubscriberPrice()
    {
        return $this->hasOne(FinSubscriberPrice::class, 'subscriber_id')->latest('created_at');
    }
}
