<?php

namespace pompong\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * pompong\Models\Genre
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\pompong\Models\Show[] $shows
 * @property mixed id
 * @property mixed genre
 * @property mixed created_at
 * @property mixed updated_at
 * @mixin \Eloquent
 */
class Genre extends Model
{
    protected $fillable = [
        'genre'
    ];

    public function shows()
    {
        return $this->belongsToMany(Show::class);
    }
}
