<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteMovie extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'year',
        'poster',
        'genre',
        'rating',
        'runtime',
        'imdb_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
