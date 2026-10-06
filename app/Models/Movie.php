<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


#[Fillable(['title', 'duration', 'released_on', 'synopsis'])]

class Movie extends Model
{
    use HasFactory;
    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
}
