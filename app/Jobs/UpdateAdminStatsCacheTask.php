<?php

namespace App\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\CacheManager\AdminDashboardService;

class UpdateAdminStatsCacheTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        AdminDashboardService::setActiveLoansBalance();

        AdminDashboardService::setInActiveLoansBalance();

        AdminDashboardService::setFulfilledLoansBalance();

        AdminDashboardService::setTransferredLoansBalance();

        AdminDashboardService::setManagedLoansBalance();

        AdminDashboardService::setVoidLoansBalance();

        AdminDashboardService::setAllPortfolioSize();

        AdminDashboardService::setInvestorsWalletBalance();

        AdminDashboardService::setBorrowersWalletBalance();

        AdminDashboardService::setBorrowersEscrowBalance();

        AdminDashboardService::setActivePaydayNoteSum();

        AdminDashboardService::setCurrentPaydayNoteSum();
    }
}
