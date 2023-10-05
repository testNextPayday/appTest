<?php

namespace App\Console\Commands\Fee;

use Illuminate\Console\Command;
use App\Unicredit\Logs\DatabaseLogger;
use App\Unicredit\Managers\PortfolioManager;

class PortFolioManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'charge:portfolio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command charges the investors portfolio based on the parameter set by admin';

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
    public function handle(PortfolioManager $manager, DatabaseLogger $dbLogger)
    {
       $manager->issuePortFolioManagementCollection();
    }
}
