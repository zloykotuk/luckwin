<?php

namespace Database\Factories;

use App\Models\History;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<History>
 */
class HistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $result = fake()->boolean();

        return [
            'user_id' => User::factory(),
            'result' => $result,
            'payout' => $result ? fake()->randomFloat(2, 1, 700) : 0,
        ];
    }
}
