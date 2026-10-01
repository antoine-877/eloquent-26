<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::orderBy('title')->get();

        return view('movies.index', [
            'movies' => $movies
        ]);
    }
}
