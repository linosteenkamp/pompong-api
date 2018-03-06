<?php

namespace pompong\Console\Commands;

class LoadShow extends LoadData
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
    protected $signature = 'pompong:load-show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Full (re)load of all SickBeard shows into pompong';

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
        $tvdbid = $this->ask('What is the tvdbid?');

        $this->updateShow($tvdbid);

        return true;
    }
}
