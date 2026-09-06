<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * The next Account ID assigned by this factory process.
     */
    protected static ?int $nextAccountId = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lowestRank = \App\Models\Rank::where('rank_order', 1)->first();

        if (static::$nextAccountId === null) {
            static::$nextAccountId = (\App\Models\User::max('account_id') ?? 0) + 1;
        }

        $accountId = static::$nextAccountId++;

        return [
            'account_id' => $accountId,
            'charactername' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'rank_id' => $lowestRank ? $lowestRank->id : null,
            'last_rank_change_at' => now(),
            'highest_rank' => $lowestRank ? $lowestRank->name : null,
        ];
    }
}
