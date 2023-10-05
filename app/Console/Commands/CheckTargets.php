<?php

namespace App\Console\Commands;

use App\Models\Target;
use App\Services\TargetService;
use Illuminate\Console\Command;

class CheckTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'target:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for expiry dates of targets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TargetService $targetService)
    {
        parent::__construct();

        $this->targetService = $targetService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $activeTargets = Target::active()->expiredYesterday()->get();

        foreach ($activeTargets as $target) {

            try {

                $this->targetService->checkForCompletedTarget($target);

                $this->targetService->update($target, ['status'=>2]);

            }catch (\Exception $e) {
                
                continue;
            }
        }
        
    }
}
