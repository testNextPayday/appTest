<?php

namespace App\Console\Commands\Remita;

use Illuminate\Console\Command;

use App\Unicredit\Collection\CollectionService;
use App\Models\Loan;
use Illuminate\Support\Facades\Log as sysLog;

class MandateChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remita:checkDdmMandates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the status of ddm mandates';

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
        $service = new CollectionService();
        
        $loans = Loan::ddmRemita()->pending()->get();
        
        foreach($loans as $loan) {
           try {

            $response = $service->checkMandateStatus($loan);
            
            if ($response->isActive())
                $loan->update(['disburse_status' => 2]);
           }catch(\Exception $e){
               sysLog::channel('remita')->info($e->getMessage());
               continue;
           }
        }
    }
}
