<?php

namespace pompong\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * pompong\Models\TmpUser
 *
 * @property mixed id
 * @property mixed name
 * @property mixed email
 * @property mixed password
 * @property mixed token
 * @property mixed created_at
 * @property mixed updated_at
 * @mixin \Eloquent
 */
class TmpUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'token',
        'created_at',
        'updated_at'
    ];
    //
}
