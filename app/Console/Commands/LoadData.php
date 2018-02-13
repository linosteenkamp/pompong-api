<?php

namespace pompong\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

use pompong\Services\SickRage;
use pompong\Services\TheTvDb;
use pompong\Models\Episode;
use pompong\Models\Genre;
use pompong\Models\Show;

class LoadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description;

    /**
     * The SickRage API Service
     *
     * @var SickRage
     */
    protected $sickRage;

    /**
     * The TheTvDb API Service
     *
     * @var TheTvDb
     */
    protected $theTvDb;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->sickRage = new SickRage();
        $this->theTvDb = new TheTvDb();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }

    protected function updateShow($showId) {
        $showData = $this->sickRage->getShow($showId);

        echo("Updating " . $showData['data']['show_name'] . "\r\n");
        if (! array_key_exists('error_msg', $showData['data'])) {
            $show = Show::firstOrNew(['id' => $showId]);

            $show->id = $showData['data']['tvdbid'];
            $show->lang  = $showData['data']['language'];
            $show->network = $showData['data']['network'];
            $show->quality = $showData['data']['quality'];
            $show->show_name = $showData['data']['show_name'];
            $show->status = $showData['data']['status'];
            $show->tvdb_id = $showData['data']['tvdbid'];
            $show->image_url = '';
            $show->overview = '';
            $show->location = $showData['data']['location'];
            $show->max_season = $showData['data']['season_list'][0];

            $this->getEpisodes($showData['data']['season_list'][0], $showId);
            $this->getGenres($showData['data']['genre'], $show);
            $this->augmentShow($showId, $show);

            $show->save();
        }

        return true;
    }

    private function augmentShow($showId, &$show)
    {
        echo("Augmenting " . $show->show_name . "\r\n");

        $tvShow = $this->theTvDb->getSeries($showId);

        if ( ! $tvShow ) { return false; };

        if (gettype($tvShow['Series']['Overview']) == "string") {
            $show->overview = $tvShow['Series']['Overview'];
        }

        if (gettype($tvShow['Series']['banner']) == "string") {
            $show->image_url = $this->getImage('http://thetvdb.com/banners/' . $tvShow['Series']['banner'], $show->id);
        }

        return true;
    }

    private function getImage($url, $show_id)
    {
        $fileName = $this->getFileName($url, $show_id);
        $this->copyRemote($url, $fileName);

        return $fileName;
    }

    private function getFileName($url, $show_id)
    {
        $fileName = basename($url);
        $temp = explode(".", $fileName);

        return 'img/shows/' . $show_id . '.' . end($temp);
    }

    private function copyRemote($fromUrl, $toFile)
    {
        $client = new Client();
        $res = $client->request('GET', $fromUrl);
        $myFile = fopen(getenv('POMPONG_IMAGE_FOLDER') . $toFile, "w+");
        fwrite($myFile, $res->getBody());
        fclose($myFile);

        return true;
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
        for ($season_no=1; $season_no <= $maxSeason; $season_no++) {
            $seasonData = $this->sickRage->getSeasons($tvdbid, $season_no);

            foreach ($seasonData['data'] as $episode_no => $value) {
                $this->updateEpisode($tvdbid, $season_no, $episode_no, false);
            }
        }
    }

    protected function updateEpisode($tvdbid, $season_no, $episode_no, $verbose = true) {
        $episodeData = $this->sickRage->getEpisode($tvdbid, $season_no, $episode_no);

        if ($verbose) {
            $show = $this->sickRage->getShow($tvdbid);
            echo(date("Y-m-d H:i:s").
                " Add S".sprintf('%02d', $season_no).
                'E'.sprintf('%02d', $episode_no).
                ' to: '.$show['data']['show_name']."\r\n");
        }

        $episode = Episode::firstOrNew([
            'show_id' => $tvdbid,
            'season' => $season_no,
            'episode_no' => $episode_no,
        ]);

        $episode->show_id = $tvdbid;
        $episode->season = $season_no;
        $episode->episode_no = $episode_no;
        $episode->name = $episodeData['data']['name'];
        $episode->status = $episodeData['data']['status'];
        $episode->airdate = $episodeData['data']['airdate'];
        $episode->description = ($episodeData['data']['description'] ?: ' ');
        $episode->file_size = $episodeData['data']['file_size'];
        $episode->location = $episodeData['data']['location'];

        $episode->save();
    }

}
