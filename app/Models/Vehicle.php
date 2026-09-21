<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_identifier',
        'plate_number',
        'type',
        'caregiver_user_id',
        'secondary_caregiver_user_id',
    ];
}
