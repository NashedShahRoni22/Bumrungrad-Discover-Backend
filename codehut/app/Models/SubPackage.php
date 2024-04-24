<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent', 'title', 'description', 'price', 'cover_photo', 'created_at', 'updated_at'
    ];
}
