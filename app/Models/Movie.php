<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'duration', 'released_on', 'synopsis'])]

class Movie extends Model
{
    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
