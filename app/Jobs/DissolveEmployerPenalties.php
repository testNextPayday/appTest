<?php

namespace App\Jobs;

use App\Models\Employer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Services\Penalty\PenaltyService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DissolveEmployerPenalties implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employer;

    /**
     * 5mins
     *  
     * @var int
     */
    public $timeout = 300;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Employer $employer)
    {
        //
        $this->employer = $employer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $employerPenalizedLoans = $this->employer->employeeLoansQuery()->penalized();
        $penaltyService = new PenaltyService();
        $employerPenalizedLoans->chunk(50, function($loans) use($penaltyService) {
            foreach($loans as $loan) {
                $penaltyService->dissolvePenalty($loan);
            }
        });
    }
}
