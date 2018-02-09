<?php

namespace pompong\Console\Commands;

use pompong\Services\SickRage;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use pompong\Models\Show;
use pompong\Models\Genre;
use pompong\Models\Episode;
use pompong\Services\TheTvDb;

class RefreshData extends Command
{
    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $sickRage;
    private $theTvDb;


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
        $this->getSickBeardShows();
        $this->augmentShows();
    }

    private function augmentShows()
    {
        $shows = Show::orderBy('show_name')->get();

        foreach($shows as $show) {

            echo("Augmenting " . $show->show_name . "\r\n");

            $tvShow = $this->theTvDb->getSeries($show->id);

            if ( ! $tvShow ) { continue; };

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
        $data = $this->sickRage->getShows();
        $show_ids = array();

        foreach ($data['data'] as $value) {
            array_push($show_ids, $value['tvdbid']);
            $showData = $this->sickRage->getShow($value['tvdbid']);

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
        Show::whereNotIn('id', $show_ids)->delete();
        
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
                $episodeData = $this->sickRage->getEpisode($tvdbid, $season_no, $episode_no);

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
    }
}
