<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inactivity extends Model
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
        'reason',
        'accepted',
    ];

    protected $casts = [
        'begin' => 'date',
        'end' => 'date',
    ];
}
