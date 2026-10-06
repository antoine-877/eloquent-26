<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Genre::create([
            "name" => "Animation"
        ]);

        Genre::create([
            "name" => "Comédie"
        ]);

        Genre::create([
            "name" => "Documentaire"
        ]);
        Genre::create([
            "name" => "Drame"
        ]);
        Genre::create([
            "name" => "Science-fiction"
        ]);
        Genre::create([
            "name" => "Thriller"
        ]);

        $genres = Genre::all();

        foreach (Movie::all() as $movie) {
            $movie->genres()->attach($genres->random(rand(1, 3)));
        }
    }
}
