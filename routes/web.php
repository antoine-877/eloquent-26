<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

Route::get('/films', [MovieController::class, 'index'])
    ->name('movies.index');
