<?php

namespace pompong\Services;

use GuzzleHttp\Client;
/**
 * Class SickRage
 *
 * @package \app\Services
 */
class SickRage
{
    private $client;

    function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://'. getenv('POMPONG_SIKBEARD_ADDRESS') .'/api/'. getenv('POMPONG_SICKBEARD_APIKEY') .'/'
        ]);
    }

    public function getShows() {
        $response = $this->client->request('GET', '?cmd=shows');

        return json_decode($response->getBody(), true);
    }

    public function getShow($showId) {
        $response = $this->client->request('GET', '?cmd=show&tvdbid='.$showId);

        return json_decode($response->getBody(), true);
    }

    public function getSeasons($showId, $season) {
        $response = $this->client->request('GET', '?cmd=show.seasons&tvdbid='. $showId . '&season=' . $season);

        return json_decode($response->getBody(), true);
    }

    public function getEpisode($showId, $season, $episode)
    {
        $response = $this->client->request('GET', '?cmd=episode&tvdbid='. $showId . '&season=' . $season . '&episode='. $episode .'&full_path=1');

        return json_decode($response->getBody(), true);
    }

}
