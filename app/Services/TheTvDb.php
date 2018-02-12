<?php

namespace pompong\Services;

use GuzzleHttp\Client;
/**
 * Class TheTvDb
 *
 * @package \pompong\Services
 */
class TheTvDb
{
    private $client;

    function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://thetvdb.com/api/'. getenv('POMPONG_THETVDB_APIKEY') .'/'
        ]);
    }

    function getSeries($show_id) {
        $result = $this->client->request(
            'GET',
            'series/' . $show_id . '/en.xml',
            ['http_errors' => false]
        );

        if ($result->getStatusCode() == 200) {
            $body = $result->getBody();
            $stringBody = (string) $body;
            $xml = simplexml_load_string($stringBody);
            $json = json_encode($xml);
            return json_decode($json,TRUE);
        } else {
            return null;
        }
    }
}
