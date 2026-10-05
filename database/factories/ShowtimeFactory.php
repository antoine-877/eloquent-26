<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Showtime>
 */
class ShowtimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'starts_at' => fake()->dateTimeBetween('now', '+30 days'),
            'price' => fake()->numberBetween(6, 12),

        ];
    }
}
