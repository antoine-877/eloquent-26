<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'duration' => fake()->numberBetween(70,180),
            'released_on'=> fake()->dateTimeBetween('-20 years', 'now')->format('Y-m-d'),

        ];
    }
}
