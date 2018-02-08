<?php

namespace pompong\Services;

use GuzzleHttp\Client;
/**
 * Class TheTvDb
 *
 * @package \app\Services
 */
class TheTvDb
{
    private $client;

    function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://'. getenv('POMPONG_SIKBEARD_ADDRESS') .'/api/'. getenv('POMPONG_SICKBEARD_APIKEY') .'/'
        ]);
    }

}
