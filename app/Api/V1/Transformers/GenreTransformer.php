<?php
/**
 * Created by PhpStorm.
 * User: linosteenkamp
 * Date: 2017/05/01
 * Time: 7:59 PM
 */

namespace pompong\Api\V1\Transformers;

use pompong\Models\Genre;
use League\Fractal\TransformerAbstract;

class GenreTransformer extends TransformerAbstract
{
    public function transform(Genre $genre)
    {
        return [
            "genre" => $genre->genre
       ];
    }

}