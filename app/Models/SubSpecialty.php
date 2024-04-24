<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSpecialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialty', 'sub_specialty', 'created_at', 'updated_at'
    ];
}
