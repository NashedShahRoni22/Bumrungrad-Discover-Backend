<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaProcessing extends Model
{
    use HasFactory;

    protected $fillable = [
        'oldPataint',
        'HnNumber',
        'PataientFirstName',
        'PataientLastName',
        'PataientCitizenship',
        'PataientGender',
        'PataientEmail',
        'PataientPhone',
        'PataientDob',
        'country',
        'mediicalCorncern',
        'passport',
        'medicalReport1',
        'medicalReport2',
        'invitationLetter', 
        'driveLink1',
        'driveLink2'
    ];
}
