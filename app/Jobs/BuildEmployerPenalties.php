<?php

namespace App\Jobs;

use App\Models\Employer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Penalty\BuildPenaltyService;

class BuildEmployerPenalties implements ShouldQueue
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

        $employerActiveLoans = $this->employer->employeeLoansQuery()->active();
        $builderService = new BuildPenaltyService();
        $employerActiveLoans->chunk(50, function($loans) use($builderService) {
            foreach($loans as $loan) {
                $builderService->build($loan);
            }
        });
    }
}
