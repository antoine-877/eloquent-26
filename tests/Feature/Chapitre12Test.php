<?php

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Room;
use App\Models\Showtime;
use Database\Seeders\GenreSeeder;
use Database\Seeders\MovieSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Chapitre 12 — plusieurs genres par film : table pivot, belongsToMany, with().

// 12.1 La pivot

test('les tables genres (name) et genre_movie (genre_id, movie_id) existent', function (): void {
    expect(Schema::hasTable('genres'))->toBeTrue('La table genres n\'existe pas.');
    expect(Schema::hasColumns('genres', ['id', 'name']))->toBeTrue('Il manque la colonne name dans la table genres.');
    expect(Schema::hasTable('genre_movie'))->toBeTrue('La table genre_movie n\'existe pas.');
    expect(Schema::hasColumns('genre_movie', ['genre_id', 'movie_id']))
        ->toBeTrue('Il manque genre_id ou movie_id dans la table genre_movie.');
})->group('chapitre12');

test('la même paire genre-film ne peut pas être enregistrée deux fois (clé primaire composée)', function (): void {
    expect(Schema::hasTable('genre_movie'))->toBeTrue('La table genre_movie n\'existe pas.');

    $movieId = insertMovie();
    $genreId = insertGenre('Drame');
    DB::table('genre_movie')->insert(['genre_id' => $genreId, 'movie_id' => $movieId]);

    expect(fn () => DB::table('genre_movie')->insert(['genre_id' => $genreId, 'movie_id' => $movieId]))
        ->toThrow(QueryException::class);
    expect(DB::table('genre_movie')->count())->toBe(1);
})->group('chapitre12');

test('genre_movie refuse un film ou un genre qui n\'existe pas (clés étrangères)', function (): void {
    expect(Schema::hasTable('genre_movie'))->toBeTrue('La table genre_movie n\'existe pas.');

    $movieId = insertMovie();
    $genreId = insertGenre('Drame');

    expect(fn () => DB::table('genre_movie')->insert(['genre_id' => $genreId, 'movie_id' => 999]))
        ->toThrow(QueryException::class);
    expect(fn () => DB::table('genre_movie')->insert(['genre_id' => 999, 'movie_id' => $movieId]))
        ->toThrow(QueryException::class);
})->group('chapitre12');

test('supprimer un film retire ses lignes de genre_movie (cascadeOnDelete)', function (): void {
    $movieId = insertMovie();
    $genreId = insertGenre('Drame');
    DB::table('genre_movie')->insert(['genre_id' => $genreId, 'movie_id' => $movieId]);

    DB::table('movies')->where('id', $movieId)->delete();

    expect(DB::table('genre_movie')->count())->toBe(0);
    expect(DB::table('genres')->count())->toBe(1);
})->group('chapitre12');

// 12.2 belongsToMany

test('attach() relie un film à deux genres : $movie->genres en compte 2', function (): void {
    $movie = Movie::find(insertMovie(['title' => 'Terminus Nord']));
    $drama = insertGenre('Drame');
    $thriller = insertGenre('Thriller');
    insertGenre('Comédie');

    $movie->genres()->attach([$drama, $thriller]);

    $genres = Movie::find($movie->id)->genres;

    expect($genres)->toHaveCount(2);
    expect($genres->first())->toBeInstanceOf(Genre::class);
    expect($genres->pluck('name')->sort()->values()->all())->toBe(['Drame', 'Thriller']);
})->group('chapitre12');

test('un genre connaît ses films : $genre->movies contient le film', function (): void {
    $movieId = insertMovie(['title' => 'Terminus Nord']);
    insertMovie(['title' => 'Sous le pont']);
    $drama = insertGenre('Drame');
    DB::table('genre_movie')->insert(['genre_id' => $drama, 'movie_id' => $movieId]);

    $movies = Genre::find($drama)->movies;

    expect($movies)->toHaveCount(1);
    expect($movies->first()->title)->toBe('Terminus Nord');
})->group('chapitre12');

test('sync([]) détache tous les genres d\'un film', function (): void {
    $movie = Movie::find(insertMovie());
    $movie->genres()->attach([insertGenre('Drame'), insertGenre('Thriller')]);

    $movie->genres()->sync([]);

    expect(Movie::find($movie->id)->genres)->toHaveCount(0);
    expect(DB::table('genre_movie')->count())->toBe(0);
    expect(DB::table('genres')->count())->toBe(2);
})->group('chapitre12');

// 12.3 Le seeder

test('GenreSeeder crée les six genres de l\'énoncé', function (): void {
    $this->seed(GenreSeeder::class);

    expect(Genre::orderBy('name')->pluck('name')->all())
        ->toBe(['Animation', 'Comédie', 'Documentaire', 'Drame', 'Science-fiction', 'Thriller']);
})->group('chapitre12');

test('après DatabaseSeeder, il y a six genres et chaque film en a entre un et trois', function (): void {
    $this->seed();

    expect(Genre::count())->toBe(6);
    expect(Movie::count())->toBeGreaterThan(0);

    foreach (Movie::with('genres')->get() as $movie) {
        expect($movie->genres->count())
            ->toBeGreaterThanOrEqual(1, "Le film « $movie->title » n'a aucun genre.")
            ->toBeLessThanOrEqual(3, "Le film « $movie->title » a plus de trois genres.");
    }
})->group('chapitre12');

// 12.4 La page genre

test('la page /genres/{id} affiche le nom du genre et ses films, et seulement eux', function (): void {
    $drama = insertGenre('Drame');
    $first = insertMovie(['title' => 'Terminus Nord']);
    $second = insertMovie(['title' => 'Marée basse']);
    insertMovie(['title' => 'Sous le pont']);
    DB::table('genre_movie')->insert([
        ['genre_id' => $drama, 'movie_id' => $first],
        ['genre_id' => $drama, 'movie_id' => $second],
    ]);

    $this->get('/genres/'.$drama)
        ->assertOk()
        ->assertSee('Drame')
        ->assertSee('Terminus Nord')
        ->assertSee('Marée basse')
        ->assertDontSee('Sous le pont');
})->group('chapitre12');

test('un numéro de genre qui n\'existe pas répond 404', function (): void {
    $drama = insertGenre('Drame');

    $this->get('/genres/'.$drama)->assertOk();
    $this->get('/genres/999')->assertNotFound();
})->group('chapitre12');

test('la route /genres/{id} s\'appelle genres.show', function (): void {
    expect(route('genres.show', 2, absolute: false))->toBe('/genres/2');
})->group('chapitre12');

// 12.5 Les genres dans la liste

test('la page /films affiche les genres de chaque film', function (): void {
    $this->seed(MovieSeeder::class);
    $drama = insertGenre('Drame');
    $scienceFiction = insertGenre('Science-fiction');
    $terminus = Movie::where('title', 'Terminus Nord')->first();
    $terminus->genres()->attach([$drama, $scienceFiction]);

    $this->get('/films')
        ->assertOk()
        ->assertSee('Drame')
        ->assertSee('Science-fiction');
})->group('chapitre12');

test('la page /films charge les genres avec with() : au plus 3 requêtes SQL pour toute la page', function (): void {
    $this->seed(MovieSeeder::class);
    $genres = [insertGenre('Drame'), insertGenre('Comédie'), insertGenre('Thriller')];

    foreach (Movie::all() as $movie) {
        $movie->genres()->attach($genres);
    }

    DB::flushQueryLog();
    DB::enableQueryLog();

    $this->get('/films')
        ->assertOk()
        ->assertSee('Drame')
        ->assertSee('Thriller');

    $queries = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queries)->toBeLessThanOrEqual(3, "La page /films a lancé $queries requêtes SQL : une par film, c'est le piège N+1.");
})->group('chapitre12');

// 12.6 Bonus : les salles (groupe à part, --group=bonus)

test('bonus : la table rooms (name, seats) existe et showtimes a une colonne room_id', function (): void {
    expect(Schema::hasTable('rooms'))->toBeTrue('La table rooms n\'existe pas.');
    expect(Schema::hasColumns('rooms', ['id', 'name', 'seats']))->toBeTrue('Il manque name ou seats dans la table rooms.');
    expect(Schema::hasColumn('showtimes', 'room_id'))->toBeTrue('La colonne room_id manque dans la table showtimes.');
})->group('bonus');

test('bonus : une séance connaît sa salle : $showtime->room->name', function (): void {
    $roomId = DB::table('rooms')->insertGetId(['name' => 'Salle 2', 'seats' => 120]);
    $showtimeId = DB::table('showtimes')->insertGetId([
        'movie_id' => insertMovie(),
        'room_id' => $roomId,
        'starts_at' => '2026-10-02 20:30:00',
        'price' => 9.5,
    ]);

    $showtime = Showtime::find($showtimeId);

    expect($showtime->room)->toBeInstanceOf(Room::class);
    expect($showtime->room->name)->toBe('Salle 2');
})->group('bonus');

test('bonus : la page /seances affiche la salle de chaque séance', function (): void {
    $roomId = DB::table('rooms')->insertGetId(['name' => 'Salle Lumière', 'seats' => 80]);
    DB::table('showtimes')->insert([
        'movie_id' => insertMovie(['title' => 'Terminus Nord']),
        'room_id' => $roomId,
        'starts_at' => now()->addDay()->format('Y-m-d').' 20:30:00',
        'price' => 9.5,
    ]);

    $this->get('/seances')
        ->assertOk()
        ->assertSee('Terminus Nord')
        ->assertSee('Salle Lumière');
})->group('bonus');
