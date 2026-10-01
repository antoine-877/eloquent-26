<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Movie::create([
            'title' => 'Terminus Nord',
            'duration' => 112,
            'released_on' => '2024-02-14',
            'synopsis' => '	Une contrôleuse de train découvre une lettre oubliée dans un wagon vide.',
        ]);
        Movie::create([
            'title' => 'La Nuit des Lucioles',
            'duration' => 98,
            'released_on' => '2024-09-04',
            'synopsis' => 'Trois amis d\'enfance se retrouvent pour une dernière nuit au bord du lac.',
        ]);
        Movie::create([
            'title' => 'Marée basse',
            'duration' => 134,
            'released_on' => '2019-11-20',
            'synopsis' => 'Sur la côte, un gardien de phare refuse de partir à la retraite.',
        ]);
        Movie::create([
            'title' => 'Les Jours sans',
            'duration' => 87,
            'released_on' => '2011-03-09',
            'synopsis' => '',
        ]);
        Movie::create([
            'title' => 'Sous le pont',
            'duration' => 105,
            'released_on' => '2003-06-25',
            'synopsis' => 'Un vieux musicien apprend le violon à une fillette du quartier.',
        ]);
    }
}
