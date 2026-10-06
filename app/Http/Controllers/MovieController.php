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
            ->with('genres')
            ->orderBy('title')
            ->get();

        return view('movies.index', [
            'movies' => $movies
        ]);
    }

    public function show($id)
    {
        $movie = Movie::findOrFail($id);

        $showtimes = $movie->showtimes()
            ->orderBy('starts_at')
            ->get();

        return view('movies.show', compact('movie', 'showtimes'));
    }
}
