<?php

namespace pompong\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * pompong\Models\PasswordReset
 *
 * @property mixed email
 * @property mixed token      
 * @property mixed created_at
 * @mixin \Eloquent
 */
class PasswordReset extends Model
{
    protected $fillable = [
        'email',
        'token'
    ];

    public function setUpdatedAt($value)
    {
        return $this;
    }
}
