<?php

namespace App\Console\Commands\Backup;

use App\Services\BackupService;
use Illuminate\Console\Command;

class DBCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleans out oudated backups';


    protected $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BackupService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //

        $this->service->runCleanUp();
    }
}
