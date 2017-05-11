<?php
/**
 * Created by PhpStorm.
 * User: linosteenkamp
 * Date: 2017/05/01
 * Time: 7:34 PM
 */

namespace pompong\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use pompong\Models\Show;


class ShowTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'seasons.users' => 'seasons',
        'genres'
    ];

    public function transform(Show $show)
    {
        return [
            "id" => $show->id,
            "network" => $show->network,
            "quality" => $show->quality,
            "show_name" => $show->show_name,
            "status" => $show->status,
            "image_url" => $show->image_url,
            "overview" => $show->overview
        ];
    }

    public function includeSeasons(Show $show)
    {
        $seasons = $show->seasons;

        return $this->collection($seasons, new SeasonTransformer);
    }

    public function includeGenres(Show $show)
    {
        $genres = $show->genres;

        return $this->collection($genres, new GenreTransformer);
    }
}