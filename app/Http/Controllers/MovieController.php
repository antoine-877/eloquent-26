<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class MovieController extends Controller
{
    public function index()
    {
        $annee = request('annee');

        $movies = Movie::query()
            ->when($annee, function ($query) use ($annee) {
                $query->whereYear('released_on', $annee);
            })
            ->orderBy('title')
            ->get();

        return view('movies.index', [
            'movies' => $movies
        ]);
    }

    public function show($id)
    {
        $movie = Movie::findOrFail($id);

        return view('movies.show', [
            'movie' => $movie
        ]);
    }
}
