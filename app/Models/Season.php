<?php

namespace pompong\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * pompong\Models\Season
 *
 * @property-read \pompong\Models\Show $show
 * @property-read \pompong\Models\User[] $users
 * @property mixed id
 * @property mixed show_id
 * @property mixed season
 * @property mixed file_size
 * @mixin \Eloquent
 */
class Season extends Model
{
    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function users()
    {
        // Limit records to logged in user
        $user = \Auth::user();
        return $this
            ->belongsToMany(User::class)
            ->wherePivot('user_id', $user['id']);
    }
}
