<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'price',
        'free_treatment',
        'npc_call',
        'diagnosis',
        'withWho',
        'img',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'free_treatment' => 'boolean',
        'npc_call' => 'boolean',
    ];
}
