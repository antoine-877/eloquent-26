<?php

namespace App\Http\Controllers;

use App\Models\Genre;


class GenreController extends Controller
{
    public function show($id)
    {
        $genre = Genre::findOrFail($id);
        $movies = $genre->movies()->orderBy('title')->get();

        return view('genres.show', [
            'genre' => $genre,
            'movies' => $movies,
        ]);
    }
}
