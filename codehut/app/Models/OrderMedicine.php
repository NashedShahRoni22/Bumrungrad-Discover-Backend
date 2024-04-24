<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMedicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription',
        'medicines',
        'quantity',
        'created_at',
        'updated_at',
    ];
}
