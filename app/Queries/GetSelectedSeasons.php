<?php
/**
 * Created by PhpStorm.
 * User: linosteenkamp
 * Date: 2017/05/07
 * Time: 5:01 PM
 */

namespace pompong\Queries;


class GetSelectedSeasons
{

    public static function exec($userId)
    {

        return  \DB::select(\DB::raw("
          SELECT DISTINCT episodes.location
          FROM season_user
            left outer JOIN seasons on seasons.id = season_user.season_id
            left outer join episodes on episodes.show_id = seasons.show_id
          WHERE
            season_user.user_id=" . $userId . "
            AND episodes.season = seasons.season
            AND episodes.status in ('Downloaded', 'Archived')
        "));
    }
}
