<?php

use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ShowtimeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MovieController::class, 'index'])
    ->name('home');

Route::get('/films', [MovieController::class, 'index'])
    ->name('movies.index');

Route::get('/films/{id}', [MovieController::class, 'show'])
    ->name('movies.show')
    ->whereNumber('id');

Route::get('/seances', [ShowtimeController::class, 'index'])
    ->name('showtimes.index');

Route::get('/genres/{id}', [GenreController::class, 'show'])
    ->name('genres.show')
    ->whereNumber('id');
