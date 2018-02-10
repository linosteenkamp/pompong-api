<?php

namespace pompong\Console\Commands;

use pompong\Models\Show;

class RefreshData extends LoadData
{
    /**
     * Create a new command instance.
     *
     * @return void
     */

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

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = $this->sickRage->getShows();
        $show_ids = array();

        foreach ($data['data'] as $value) {
            array_push($show_ids, $value['tvdbid']);
            $this->updateShow($value['tvdbid']);
        }

        Show::whereNotIn('id', $show_ids)->delete();

        return true;
    }
}
