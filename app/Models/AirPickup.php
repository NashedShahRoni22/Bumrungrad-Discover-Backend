<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirPickup extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment',
        'air_ticket',
        'passenger',
        'created_at',
        'updated_at',
    ];
}
