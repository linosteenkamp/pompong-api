<?php

namespace pompong\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
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
        try {
            $response = $this->client->request('GET', '?cmd=shows');
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                echo(date("Y-m-d H:i:s") . " " . Psr7\str($e->getResponse()) . "\r\n");
                return null;
            }
        }

        return json_decode($response->getBody(), true);
    }

    public function getShow($showId) {
        try {
            $response = $this->client->request('GET', '?cmd=show&tvdbid='.$showId);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                echo(date("Y-m-d H:i:s") . " " . Psr7\str($e->getResponse()) . "\r\n");
                return null;
            }
        }

        return json_decode($response->getBody(), true);
    }

    public function getSeasons($showId, $season) {
        try {
            $response = $this->client->request('GET', '?cmd=show.seasons&tvdbid='. $showId . '&season=' . $season);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                echo(date("Y-m-d H:i:s") . " " . Psr7\str($e->getResponse()) . "\r\n");
                return null;
            }
        }

        return json_decode($response->getBody(), true);
    }

    public function getEpisode($showId, $season, $episode)
    {
        try {
            $response = $this->client->request('GET', '?cmd=episode&tvdbid='. $showId . '&season=' . $season . '&episode='. $episode .'&full_path=1');
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                echo(date("Y-m-d H:i:s") . " " . Psr7\str($e->getResponse()) . "\r\n");
                return null;
            }
        }

        return json_decode($response->getBody(), true);
    }

    public function getHistory() {
        try {
            $response = $this->client->request('GET', '?cmd=history&limit=0&type=downloaded');
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                echo(date("Y-m-d H:i:s") . " " . Psr7\str($e->getResponse()) . "\r\n");
                return null;
            }
        }

        return json_decode($response->getBody(), true);
    }

    public function clearHistory() {
        try {
            $response = $this->client->request('GET', '?cmd=history.clear');
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                echo(date("Y-m-d H:i:s") . " " . Psr7\str($e->getResponse()) . "\r\n");
                return null;
            }
        }

        return json_decode($response->getBody(), true);
    }

}
