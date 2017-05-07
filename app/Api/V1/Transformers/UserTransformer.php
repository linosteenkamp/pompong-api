<?php
/**
 * Created by PhpStorm.
 * User: linosteenkamp
 * Date: 2017/05/01
 * Time: 8:09 PM
 */

namespace pompong\Api\V1\Transformers;


use pompong\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            "id" => $user->id,
        ];
    }
}