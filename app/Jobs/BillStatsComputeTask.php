<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\BillStatService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\CacheManager\CacheConstants;

class BillStatsComputeTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 30mins
     *  
     * @var int
     */
    public $timeout = 1800;

    public $tries = 3;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BillStatService $recordService)
    {
        //
        $data = $recordService->computeBillRecords();

        Cache::forever(CacheConstants::BILL_CAT_STATS, $data);
    }
}
