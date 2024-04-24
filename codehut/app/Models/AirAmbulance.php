<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirAmbulance extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_date',
        'air_ticket',
        'prescription',
        'description',
        'created_at',
        'updated_at',
    ];
}
