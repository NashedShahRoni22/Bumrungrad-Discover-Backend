<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'specialty',
        'sub_specialty',
        'lang',
        'gender',
        'school',
        'certificates',
        'fellowships',
        'interests',
        'experiences',
        'researches',
        'article',
        'day',
        'arrival',
        'leave',
        'location',
        'shift',
        'created_at',
        'updated_at',
    ];
}
