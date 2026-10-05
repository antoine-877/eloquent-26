<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Database\Seeder;
use Database\Seeders\MovieSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MovieSeeder::class,
        ]);

        $movies = Movie::factory()->count(10)->create();

        foreach ($movies as $movie) {
            Showtime::factory()->count(3)->create([
                'movie_id' => $movie->id,
            ]);
        }
    }
}
