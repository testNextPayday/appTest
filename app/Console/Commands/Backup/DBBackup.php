<?php

namespace App\Console\Commands\Backup;

use Illuminate\Console\Command;
use App\Services\BackupService;

class DBBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run {--type=daily}';

    
    /**
     * The service responsible for handling the backup
     *
     * @var \App\Services\BackupService
     */
    protected $backupService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backs up the database to a specified disk at runtime';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(BackupService $backupService)
    {
        parent::__construct();

        $this->backupService = $backupService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $option = $this->option('type');

        $this->backupService->setBackupType($option);

        $this->backupService->runBackup();
    }
}
