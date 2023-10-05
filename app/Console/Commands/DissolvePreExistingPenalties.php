<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;
use App\Services\Penalty\PenaltyService;

class DissolvePreExistingPenalties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dissolve:penalties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dissolve penalties on all loans that are penalized';

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
        $penaltyService = new PenaltyService();
        
        // Get all loans on penalty and dissolve them
        Loan::penalized()->chunk(10, function($loans) use ($penaltyService){
            foreach($loans as $loan) {
                $penaltyService->dissolvePenalty($loan);
            }
        });
    }
}
