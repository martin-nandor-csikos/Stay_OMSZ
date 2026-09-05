<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'salary',
        'rank_order',
        'requires_exam',
        'minimum_successful_weeks',
        'is_leader',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requires_exam' => 'boolean',
        'is_leader' => 'boolean',
    ];

    /**
     * The users that hold this rank.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
