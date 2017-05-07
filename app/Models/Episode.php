<?php

namespace pompong\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * pompong\Models\Episode
 *
 * @property-read \pompong\Models\Show $show
 * @property mixed id          
 * @property mixed show_id
 * @property mixed season
 * @property mixed episode_no
 * @property mixed name
 * @property mixed status
 * @property mixed airdate
 * @property mixed description
 * @property mixed file_size
 * @property mixed location
 * @property mixed created_at
 * @property mixed updated_at
 * @mixin \Eloquent
 */
class Episode extends Model
{
    protected $fillable = [
        'show_id',
        'season',
        'episode_no',
        'status',
        'airdate',
        'description',
        'file_size',
        'location'
    ];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

}
