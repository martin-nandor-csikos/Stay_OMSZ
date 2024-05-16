<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DutyTime extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'begin',
        'end',
        'user_id',
        'minutes',
    ];

    protected $casts = [
        'begin' => 'datetime',
        'end' => 'datetime',
    ];
}
