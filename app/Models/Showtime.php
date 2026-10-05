<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['movie_id', 'starts_at', 'price'])]
class Showtime extends Model
{
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
        ];
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
