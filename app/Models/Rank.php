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
    ];

    /**
     * The users that hold this rank.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
