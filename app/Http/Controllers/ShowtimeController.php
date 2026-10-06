<?php

namespace App\Http\Controllers;

use App\Models\Showtime;

class ShowtimeController extends Controller
{
    public function index()
    {
        $showtimes = Showtime::with(['movie', 'room'])
            ->orderBy('starts_at')
            ->get();

        return view('showtimes.index', compact('showtimes'));
    }
}
