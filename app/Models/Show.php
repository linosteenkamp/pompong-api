<?php

namespace pompong\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * pompong\Models\Show
 *
 * @property int id
 * @property string lang
 * @property string network
 * @property string quality
 * @property string show_name
 * @property string status
 * @property int tvdb_id
 * @property string image_url
 * @property string overview
 * @property string location
 * @property int max_season
 * @property-read \Illuminate\Database\Eloquent\Collection|\pompong\Models\Episode[] $episodes
 * @property-read \Illuminate\Database\Eloquent\Collection|\pompong\Models\Genre[] $genres
 * @property-read \Illuminate\Database\Eloquent\Collection|\pompong\Models\Season[] $seasons
 * @mixin \Eloquent
 */
class Show extends Model
{
    protected $fillable = [
        'id',
        'lang',
        'network',
        'quality',
        'show_name',
        'status',
        'tvdb_id',
        'image_url',
        'overview',
        'location',
        'max_season'
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
}
