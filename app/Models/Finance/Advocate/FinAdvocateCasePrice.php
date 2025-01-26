<?php

namespace App\Models\Finance\Advocate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinAdvocateCasePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 'date_end', 'price', 'client_work_id'
    ];
}
