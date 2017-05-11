<?php
/**
 * Created by PhpStorm.
 * User: linosteenkamp
 * Date: 2017/05/01
 * Time: 7:59 PM
 */

namespace pompong\Api\V1\Transformers;

use pompong\Models\Season;
use League\Fractal\TransformerAbstract;

class SeasonTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'users'
    ];

    public function transform(Season $season)
    {
        return [
            "id" => $season->id,
            "season" => $season->season,
            "file_size" => $season->file_size,
       ];
    }

    public function includeUsers(Season $season)
    {
        $users = $season->users;

        return $this->collection($users, new UserTransformer);
    }

}