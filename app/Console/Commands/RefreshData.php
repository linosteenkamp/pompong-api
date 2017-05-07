<?php

namespace pompong\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use pompong\Models\Show;

class RefreshData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pompong:refresh-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rrefresh show data from SickBeard';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $this->getSickBeardShows();
        $this->augmentShows();
    }

    private function augmentShows()
    {
        $shows = Show::orderBy('show_name')->get();
        $client = new Client();

        foreach($shows as $show) {

            echo("Augmenting " . $show->show_name . "\r\n");

            $res = $client->request(
                'GET',
                'http://thetvdb.com/api/'. getenv('POMPONG_THETVDB_APIKEY') .'/series/' . $show->id . '/en.xml',
                ['http_errors' => false]
            );

            if ($res->getStatusCode() == 200) {
                $body = $res->getBody();
                $stringBody = (string) $body;
                $xml = simplexml_load_string($stringBody);
                $json = json_encode($xml);
                $tvShow = json_decode($json,TRUE);
            } else {
                continue;
            }

            if (gettype($tvShow['Series']['Overview']) == "string") {
                $show->overview = $tvShow['Series']['Overview'];
            }

            if (gettype($tvShow['Series']['banner']) == "string") {
                $show->image_url = $this->getImage('http://thetvdb.com/banners/' . $tvShow['Series']['banner'], $show->id);
            }

            $show->save();
        }
    }

    private function getImage($url, $show_id)
    {
        $fileName = $this->getFileName($url, $show_id);
        $this->copyRemote($url, $fileName);

        return $fileName;
    }

    Private function getFileName($url, $show_id)
    {
        $fileName = basename($url);
        $temp = explode(".", $fileName);
        $fileName = 'img/shows/' . $show_id . '.' . end($temp);

        return $fileName;
    }

    private function copyRemote($fromUrl, $toFile)
    {
        $client = new Client();
        $res = $client->request('GET', $fromUrl);
        $myFile = fopen('public/' . $toFile, "w+");
        fwrite($myFile, $res->getBody());
        fclose($myFile);

        return true;
    }

    private function getSickBeardShows()
    {
        $client = new Client(['base_uri' => 'http://'. getenv('POMPONG_SIKBEARD_ADDRESS') .'/api/'. getenv('POMPONG_SICKBEARD_APIKEY') .'/']);

        $response = $client->request('GET', '?cmd=shows');

        $data = json_decode($response->getBody(), true);

        foreach ($data['data'] as $value) {
            $response = $client->request('GET', '?cmd=show&tvdbid='.$value['tvdbid']);
            $showData = json_decode($response->getBody(), true);

            echo("Updating " . $value['show_name'] . "\r\n");
            if (! array_key_exists('error_msg', $showData['data'])) {
                $show = Show::firstOrNew(['id' => $value['tvdbid']]);

                $this->getGenres($showData['data']['genre'], $show);
                $this->getEpisodes($showData['data']['season_list'][0], $value['tvdbid']);

                $show->id = $value['tvdbid'];
                $show->lang  = $value['language'];
                $show->network = $value['network'];
                $show->quality = $value['quality'];
                $show->show_name = $value['show_name'];
                $show->status = $value['status'];
                $show->tvdb_id = $value['tvdbid'];
                $show->overview = '';
                $show->location = $showData['data']['location'];
                $show->max_season = $showData['data']['season_list'][0];

                $show->save();
            }
        }
    }

    private function getGenres($genres, &$show)
    {
        $showGenres = [];

        foreach($genres as $genre) {
            $tmpGenre = Genre::firstOrNew(['genre' => $genre]);
            $tmpGenre->genre = $genre;
            $tmpGenre->save();

            array_push($showGenres, $tmpGenre->id);
        }

        $show->genres()->sync($showGenres);
    }

    private function getEpisodes($maxSeason, $tvdbid)
    {
        $client = new Client(['base_uri' => 'http://'. getenv('POMPONG_SIKBEARD_ADDRESS') .'/api/'. getenv('POMPONG_SICKBEARD_APIKEY') .'/']);

        for ($i=1; $i <= $maxSeason; $i++) {
            $response = $client->request('GET', '?cmd=show.seasons&tvdbid='. $tvdbid . '&season=' . $i);
            $seasonData = json_decode($response->getBody(), true);

            foreach ($seasonData['data'] as $key => $value) {

                $response = $client->request(
                    'GET',
                    '?cmd=episode&tvdbid='. $tvdbid . '&season=' . $i . '&episode='. $key .'&full_path=1'
                );
                $episodeData = json_decode($response->getBody(), true);


                $episode = Episode::firstOrNew([
                    'show_id' => $tvdbid,
                    'season' => $i,
                    'episode_no' => $key,
                ]);

                $episode->show_id = $tvdbid;
                $episode->season = $i;
                $episode->episode_no = $key;
                $episode->name = $episodeData['data']['name'];
                $episode->status = $episodeData['data']['status'];
                $episode->airdate = $episodeData['data']['airdate'];
                $episode->description = ($episodeData['data']['description'] ?: ' ');
                $episode->file_size = $episodeData['data']['file_size'];
                $episode->location = $episodeData['data']['location'];

                $episode->save();
            }
        }
    }
}
