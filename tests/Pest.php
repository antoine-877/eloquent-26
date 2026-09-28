<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Chaque test de tests/Feature démarre sur une base SQLite en mémoire, migrée
| à neuf (RefreshDatabase). Votre fichier database/database.sqlite n'est
| jamais touché par les tests.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Fonctions d'aide
|--------------------------------------------------------------------------
|
| Ces fonctions écrivent directement dans les tables avec DB::table(), sans
| passer par vos modèles. Un test sur une migration ou sur une relation ne
| dépend ainsi que de ce qu'il vérifie.
|
*/

/** Insère un film dans la table movies et renvoie son id. */
function insertMovie(array $attributes = []): int
{
    return DB::table('movies')->insertGetId($attributes + [
        'title' => 'Film de test',
        'duration' => 100,
        'released_on' => '2020-01-01',
        'synopsis' => null,
    ]);
}

/** Insère une séance dans la table showtimes et renvoie son id. */
function insertShowtime(int $movieId, string $startsAt, float $price = 9.5): int
{
    return DB::table('showtimes')->insertGetId([
        'movie_id' => $movieId,
        'starts_at' => $startsAt,
        'price' => $price,
    ]);
}

/** Insère un genre dans la table genres et renvoie son id. */
function insertGenre(string $name): int
{
    return DB::table('genres')->insertGetId(['name' => $name]);
}
