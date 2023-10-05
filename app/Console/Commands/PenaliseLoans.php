<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\Penalty\MonthlyPenaltyService;

class PenaliseLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'penalise:loans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Penalize defaulting loans ';

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
    public function handle(MonthlyPenaltyService $penaltyAccrueService)
    {
        //
        try {

            DB::beginTransaction();

            $penaltyAccrueService->penalizeDefaultingLoans();

            DB::commit();
            
        }catch(\Exception $e) {
          
            DB::rollback();

           
        }
        
    }
}
