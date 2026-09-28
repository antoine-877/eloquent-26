<?php

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Chapitre 11 — deux tables : clé étrangère, belongsTo et hasMany, fabriques, pages.

// 11.1 La table liée

test('la table showtimes existe avec les colonnes movie_id, starts_at, price et les timestamps', function (): void {
    expect(Schema::hasTable('showtimes'))->toBeTrue('La table showtimes n\'existe pas.');
    expect(Schema::hasColumns('showtimes', ['id', 'movie_id', 'starts_at', 'price', 'created_at', 'updated_at']))
        ->toBeTrue('Il manque au moins une colonne dans la table showtimes.');
})->group('chapitre11');

test('starts_at est une date avec heure et price un nombre décimal', function (): void {
    expect(Schema::getColumnType('showtimes', 'starts_at'))->toBeIn(['datetime', 'timestamp']);
    expect(Schema::getColumnType('showtimes', 'price'))->toBeIn(['numeric', 'decimal']);
})->group('chapitre11');

test('une séance ne peut pas pointer vers un film qui n\'existe pas (clé étrangère)', function (): void {
    expect(Schema::hasTable('showtimes'))->toBeTrue('La table showtimes n\'existe pas.');

    $movieId = insertMovie();
    insertShowtime($movieId, '2026-10-02 20:30:00');

    expect(fn () => insertShowtime(999, '2026-10-02 20:30:00'))->toThrow(QueryException::class);
    expect(DB::table('showtimes')->count())->toBe(1);
})->group('chapitre11');

test('supprimer un film supprime aussi ses séances (cascadeOnDelete)', function (): void {
    $movieId = insertMovie();
    $otherId = insertMovie(['title' => 'Autre film']);
    insertShowtime($movieId, '2026-10-02 14:00:00');
    insertShowtime($movieId, '2026-10-02 20:30:00');
    insertShowtime($otherId, '2026-10-03 18:00:00');

    DB::table('movies')->where('id', $movieId)->delete();

    expect(DB::table('showtimes')->count())->toBe(1);
    expect(DB::table('showtimes')->where('movie_id', $movieId)->count())->toBe(0);
})->group('chapitre11');

// 11.2 Les deux relations

test('une séance connaît son film : $showtime->movie->title', function (): void {
    $movieId = insertMovie(['title' => 'Terminus Nord']);
    $showtimeId = insertShowtime($movieId, '2026-10-02 20:30:00');

    $showtime = Showtime::find($showtimeId);

    expect($showtime->movie)->toBeInstanceOf(Movie::class);
    expect($showtime->movie->title)->toBe('Terminus Nord');
})->group('chapitre11');

test('un film connaît ses séances : $movie->showtimes en compte 2 après deux créations', function (): void {
    $movieId = insertMovie(['title' => 'Terminus Nord']);
    $otherId = insertMovie(['title' => 'Autre film']);
    insertShowtime($movieId, '2026-10-02 14:00:00');
    insertShowtime($movieId, '2026-10-02 20:30:00');
    insertShowtime($otherId, '2026-10-03 18:00:00');

    $showtimes = Movie::find($movieId)->showtimes;

    expect($showtimes)->toHaveCount(2);
    expect($showtimes->first())->toBeInstanceOf(Showtime::class);
})->group('chapitre11');

// 11.3 Les fabriques

test('Movie::factory()->count(3)->create() crée trois films qui durent entre 70 et 180 minutes', function (): void {
    Movie::factory()->count(3)->create();

    expect(Movie::count())->toBe(3);

    foreach (Movie::all() as $movie) {
        expect($movie->title)->toBeString()->not->toBeEmpty();
        expect($movie->duration)->toBeGreaterThanOrEqual(70)->toBeLessThanOrEqual(180);
    }
})->group('chapitre11');

test('les films de fabrique sont sortis dans les vingt dernières années', function (): void {
    Movie::factory()->count(3)->create();

    foreach (Movie::all() as $movie) {
        $releasedOn = Carbon::parse($movie->released_on);

        expect($releasedOn->greaterThanOrEqualTo(now()->subYears(20)->startOfDay()))->toBeTrue("$releasedOn est trop ancien.");
        expect($releasedOn->lessThanOrEqualTo(now()))->toBeTrue("$releasedOn est dans le futur.");
    }
})->group('chapitre11');

test('Showtime::factory() crée une séance pour le film donné, à un prix entre 6 et 12', function (): void {
    $movieId = insertMovie();

    $showtime = Showtime::factory()->create(['movie_id' => $movieId]);
    $found = Showtime::find($showtime->id);

    expect(Showtime::count())->toBe(1);
    expect($found->movie_id)->toBe($movieId);
    expect($found->starts_at)->not->toBeNull();
    expect((float) $found->price)->toBeGreaterThanOrEqual(6)->toBeLessThanOrEqual(12);
})->group('chapitre11');

// 11.4 Le seeder avec fabriques

test('DatabaseSeeder crée 15 films et 30 séances', function (): void {
    $this->seed();

    expect(Movie::count())->toBe(15);
    expect(Showtime::count())->toBe(30);
})->group('chapitre11');

test('après DatabaseSeeder, chaque film de fabrique a trois séances', function (): void {
    $this->seed();

    $movies = Movie::whereNotIn('title', ['Terminus Nord', 'La Nuit des Lucioles', 'Marée basse', 'Les Jours sans', 'Sous le pont'])->get();

    expect($movies)->toHaveCount(10);

    foreach ($movies as $movie) {
        expect($movie->showtimes)->toHaveCount(3);
    }
})->group('chapitre11');

// 11.5 Le programme

test('la page /seances affiche les séances triées par heure de début, avec le titre du film', function (): void {
    $tomorrow = now()->addDay()->format('Y-m-d');
    $dayAfter = now()->addDays(2)->format('Y-m-d');

    $first = insertMovie(['title' => 'Marée basse']);
    $second = insertMovie(['title' => 'Terminus Nord']);
    $third = insertMovie(['title' => 'La Nuit des Lucioles']);

    insertShowtime($third, "$dayAfter 10:00:00");
    insertShowtime($second, "$tomorrow 20:30:00");
    insertShowtime($first, "$tomorrow 14:00:00");

    $this->get('/seances')
        ->assertOk()
        ->assertSeeInOrder(['Marée basse', 'Terminus Nord', 'La Nuit des Lucioles']);
})->group('chapitre11');

test('la page /seances affiche le prix de chaque séance', function (): void {
    $movieId = insertMovie(['title' => 'Terminus Nord']);
    insertShowtime($movieId, now()->addDay()->format('Y-m-d').' 20:30:00', 7.25);

    $html = $this->get('/seances')->assertOk()->getContent();

    expect($html)->toMatch('/7[.,]25/');
})->group('chapitre11');

test('la route /seances s\'appelle showtimes.index', function (): void {
    expect(route('showtimes.index', absolute: false))->toBe('/seances');
})->group('chapitre11');

// 11.6 La fiche complétée

test('la fiche d\'un film liste ses séances', function (): void {
    $tomorrow = now()->addDay()->format('Y-m-d');
    $movieId = insertMovie(['title' => 'Terminus Nord']);
    insertShowtime($movieId, "$tomorrow 14:15:00");
    insertShowtime($movieId, "$tomorrow 21:45:00");

    $html = $this->get('/films/'.$movieId)
        ->assertOk()
        ->assertSee('Terminus Nord')
        ->assertDontSee('Aucune séance')
        ->getContent();

    expect($html)->toMatch('/14\s*[:h]\s*15/');
    expect($html)->toMatch('/21\s*[:h]\s*45/');
})->group('chapitre11');

test('la fiche d\'un film sans séance affiche « Aucune séance »', function (): void {
    $withShowtimes = insertMovie(['title' => 'Terminus Nord']);
    $withoutShowtimes = insertMovie(['title' => 'Les Jours sans']);
    insertShowtime($withShowtimes, now()->addDay()->format('Y-m-d').' 20:30:00');

    $this->get('/films/'.$withoutShowtimes)
        ->assertOk()
        ->assertSee('Les Jours sans')
        ->assertSee('Aucune séance');
})->group('chapitre11');
