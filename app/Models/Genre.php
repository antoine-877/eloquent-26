<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name'])]
class Genre extends Model
{
    protected $table = 'genres';

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }
}
