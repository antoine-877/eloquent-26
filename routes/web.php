<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ShowtimeController;

Route::get('/films', [MovieController::class, 'index'])
    ->name('movies.index');

Route::get('/films/{id}', [MovieController::class, 'show'])
    ->name('movies.show')
    ->whereNumber('id');

Route::get('/seances', [ShowtimeController::class, 'index'])
    ->name('showtimes.index');
