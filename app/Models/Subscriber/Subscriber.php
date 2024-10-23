<?php

namespace App\Models\Subscriber;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'edrpou', 'contract_date', 'responsible_person_id', 'contact_person_name', 'contact_person_phone', 'director_person_name', 'director_person_phone', 'legal_address', 'actual_address', 'info'
    ];
}
