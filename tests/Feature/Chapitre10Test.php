<?php

use App\Models\Movie;
use Database\Seeders\MovieSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Chapitre 10 — une table : migration, modèle, seeder, requêtes, pages.

const MOVIE_TITLES_IN_ORDER = [
    'La Nuit des Lucioles',
    'Les Jours sans',
    'Marée basse',
    'Sous le pont',
    'Terminus Nord',
];

// 10.1 La table

test('la table movies existe avec les colonnes title, duration, released_on, synopsis et les timestamps', function (): void {
    expect(Schema::hasTable('movies'))->toBeTrue('La table movies n\'existe pas.');
    expect(Schema::hasColumns('movies', ['id', 'title', 'duration', 'released_on', 'synopsis', 'created_at', 'updated_at']))
        ->toBeTrue('Il manque au moins une colonne dans la table movies.');
})->group('chapitre10');

test('duration est une colonne d\'entiers et released_on une colonne de dates', function (): void {
    expect(Schema::getColumnType('movies', 'duration'))->toBeIn(['integer', 'smallint', 'int']);
    expect(Schema::getColumnType('movies', 'released_on'))->toBe('date');
})->group('chapitre10');

test('un film peut être enregistré sans synopsis (synopsis accepte null)', function (): void {
    $id = insertMovie(['title' => 'Les Jours sans', 'synopsis' => null]);

    expect(DB::table('movies')->count())->toBe(1);
    expect(DB::table('movies')->find($id)->synopsis)->toBeNull();
})->group('chapitre10');

// 10.2 Le modèle

test('Movie::create() écrit une ligne dans la table movies', function (): void {
    Movie::create([
        'title' => 'Terminus Nord',
        'duration' => 112,
        'released_on' => '2024-02-14',
        'synopsis' => 'Une contrôleuse de train découvre une lettre oubliée dans un wagon vide.',
    ]);

    expect(Movie::count())->toBe(1);
    expect(DB::table('movies')->count())->toBe(1);
})->group('chapitre10');

test('un film créé avec Movie::create() se relit avec ses valeurs', function (): void {
    $movie = Movie::create([
        'title' => 'Terminus Nord',
        'duration' => 112,
        'released_on' => '2024-02-14',
        'synopsis' => 'Une contrôleuse de train découvre une lettre oubliée dans un wagon vide.',
    ]);

    $found = Movie::find($movie->id);

    expect($found->title)->toBe('Terminus Nord');
    expect($found->duration)->toBe(112);
    expect((string) $found->released_on)->toStartWith('2024-02-14');
})->group('chapitre10');

test('sans synopsis, Movie::create() laisse synopsis à null', function (): void {
    $movie = Movie::create([
        'title' => 'Les Jours sans',
        'duration' => 87,
        'released_on' => '2011-03-09',
    ]);

    expect(Movie::find($movie->id)->synopsis)->toBeNull();
})->group('chapitre10');

// 10.3 Le seeder

test('MovieSeeder crée exactement les cinq films de l\'énoncé', function (): void {
    $this->seed(MovieSeeder::class);

    expect(Movie::count())->toBe(5);
    expect(Movie::orderBy('title')->pluck('title')->all())->toBe(MOVIE_TITLES_IN_ORDER);
})->group('chapitre10');

test('MovieSeeder enregistre les durées, les dates de sortie et les synopsis de l\'énoncé', function (): void {
    $this->seed(MovieSeeder::class);

    expect(Movie::where('title', 'Terminus Nord')->first()->duration)->toBe(112);
    expect(Movie::orderBy('title')->pluck('duration')->all())->toBe([98, 87, 134, 105, 112]);
    expect(Movie::orderBy('title')->pluck('released_on')->map(fn ($date): string => substr((string) $date, 0, 10))->all())
        ->toBe(['2024-09-04', '2011-03-09', '2019-11-20', '2003-06-25', '2024-02-14']);
    expect(Movie::where('title', 'Les Jours sans')->first()->synopsis)->toBeNull();
    expect(Movie::where('title', 'Marée basse')->first()->synopsis)
        ->toBe('Sur la côte, un gardien de phare refuse de partir à la retraite.');
})->group('chapitre10');

test('DatabaseSeeder appelle MovieSeeder : après db:seed, les cinq films sont en base', function (): void {
    $this->seed();

    expect(Movie::whereIn('title', MOVIE_TITLES_IN_ORDER)->count())->toBe(5);
})->group('chapitre10');

// 10.4 La liste

test('la page /films répond 200 et affiche les cinq titres dans l\'ordre alphabétique', function (): void {
    $this->seed(MovieSeeder::class);

    $this->get('/films')
        ->assertOk()
        ->assertSee('Marée basse')
        ->assertSeeInOrder(MOVIE_TITLES_IN_ORDER);
})->group('chapitre10');

test('la page /films présente les films dans un tableau HTML', function (): void {
    $this->seed(MovieSeeder::class);

    $this->get('/films')
        ->assertOk()
        ->assertSee('<table', escape: false);
})->group('chapitre10');

test('la route /films s\'appelle movies.index', function (): void {
    expect(route('movies.index', absolute: false))->toBe('/films');
})->group('chapitre10');

// 10.5 La fiche

test('la fiche d\'un film affiche son titre, sa durée en « 1 h 52 » et son synopsis', function (): void {
    $this->seed(MovieSeeder::class);
    $movie = Movie::where('title', 'Terminus Nord')->first();

    $this->get('/films/'.$movie->id)
        ->assertOk()
        ->assertSee('Terminus Nord')
        ->assertSee('1 h 52')
        ->assertSee('Une contrôleuse de train découvre une lettre oubliée dans un wagon vide.');
})->group('chapitre10');

test('la durée d\'un autre film est aussi écrite en heures et minutes : 98 minutes donnent « 1 h 38 »', function (): void {
    $this->seed(MovieSeeder::class);
    $movie = Movie::where('title', 'La Nuit des Lucioles')->first();

    $this->get('/films/'.$movie->id)
        ->assertOk()
        ->assertSee('1 h 38');
})->group('chapitre10');

test('un numéro de film qui n\'existe pas répond 404 (findOrFail)', function (): void {
    $this->seed(MovieSeeder::class);
    $movie = Movie::where('title', 'Terminus Nord')->first();

    $this->get('/films/'.$movie->id)->assertOk();
    $this->get('/films/999')->assertNotFound();
})->group('chapitre10');

test('la route /films/{id} s\'appelle movies.show', function (): void {
    expect(route('movies.show', 4, absolute: false))->toBe('/films/4');
})->group('chapitre10');

// 10.6 Le filtre

test('/films?annee=2024 ne garde que les deux films sortis en 2024', function (): void {
    $this->seed(MovieSeeder::class);

    $this->get('/films?annee=2024')
        ->assertOk()
        ->assertSee('Terminus Nord')
        ->assertSee('La Nuit des Lucioles')
        ->assertDontSee('Marée basse')
        ->assertDontSee('Les Jours sans')
        ->assertDontSee('Sous le pont')
        ->assertDontSee('Aucun film');
})->group('chapitre10');

test('/films?annee=1999 n\'affiche aucun titre, seulement le message « Aucun film »', function (): void {
    $this->seed(MovieSeeder::class);

    $response = $this->get('/films?annee=1999')
        ->assertOk()
        ->assertSee('Aucun film');

    foreach (MOVIE_TITLES_IN_ORDER as $title) {
        $response->assertDontSee($title);
    }
})->group('chapitre10');
