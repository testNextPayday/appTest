<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\BillStatsComputeTask;

class BillStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bill:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute the statistics details of bills based on category';

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
        dispatch(new BillStatsComputeTask());
    }
}
