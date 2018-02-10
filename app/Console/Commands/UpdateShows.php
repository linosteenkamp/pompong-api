<?php

namespace pompong\Console\Commands;

class UpdateShows extends LoadData
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pompong:update-shows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update pompong with latest SickRage downloads';

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
        $data = $this->sickRage->getHistory();
        $this->sickRage->clearHistory();

        foreach ($data['data'] as $value) {
            $this->updateShow($value['tvdbid']);
        }

        return true;
    }
}
