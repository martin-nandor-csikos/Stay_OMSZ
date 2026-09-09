<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'account_id',
        'plus_points',
        'penalty_points',
        'last_plus_point_at',
        'last_penalty_point_at',
        'charactername',
        'username',
        'password',
        'adminLevel',
        'rank_id',
        'department',
        'last_department_change_at',
        'last_rank_change_at',
        'highest_rank',
        'salary',
        'salary_tooltip',
        'successful_weeks',
        'promoted_from_rank',
        'promoted_to_rank',
        'rank_change_type',
        'closed_week_salary',
        'closed_week_bonus',
        'closed_week_calculation',
        'closed_week_message',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'adminLevel',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'has_logged_in' => 'boolean',
        'last_department_change_at' => 'datetime',
        'last_rank_change_at' => 'datetime',
        'last_plus_point_at' => 'datetime',
        'last_penalty_point_at' => 'datetime',
    ];

    /**
     * The rank assigned to the user.
     */
    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }
}
