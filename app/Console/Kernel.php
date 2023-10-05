<?php

namespace App\Console;

use Illuminate\Support\Facades\Log;
use App\Console\Commands\FulFilLoans;

use App\Console\Commands\CheckTargets;
use App\Console\Commands\PenaliseLoans;
use App\Jobs\UpdateAdminStatsCacheTask;
use App\Console\Commands\BillStatistics;
use App\Console\Commands\Backup\DBBackup;
use App\Console\Commands\Collectors\Card;
use App\Console\Commands\Backup\DBCleanup;
use App\Console\Commands\BirthdayMessages;
use App\Console\Commands\MoveFundToWallet;
use App\Console\Commands\SweepPenableLoans;
use Illuminate\Console\Scheduling\Schedule;

use App\Console\Commands\Bills\PayWeeklyBills;
use App\Console\Commands\Sweepers\LoanSweeper;

use App\Console\Commands\Bills\PayMonthlyBills;
use App\Console\Commands\Remita\MandateChecker;
use App\Console\Commands\UpdateAdminStatsCache;
use App\Console\Commands\CheckForPenalablePlans;
use App\Console\Commands\Payments\VerifyBuffers;

use App\Console\Commands\PromissoryNoteInterest;

use App\Console\Commands\SweepAutoSweepingLoans;

use App\Console\Commands\Sweepers\BucketSweeper;
use App\Console\Commands\Fee\PortFolioManagement;
use App\Console\Commands\Remita\SendInstructions;
use App\Console\Commands\Utilities\InvoiceIssuer;
use App\Console\Commands\Sweepers\EmployerSweeper;
use App\Console\Commands\Sweepers\LoanPeakSweeper;
use App\Console\Commands\Remita\CancelInstructions;
use App\Console\Commands\Remita\UpdateInstructions;
use App\Console\Commands\Repayments\SettleInvestors;
use App\Console\Commands\Sweepers\BucketPeakSweeper;
use App\Console\Commands\Collectors\SweepManagedLoans;
use App\Console\Commands\Sweepers\EmployerPeakSweeper;
use App\Console\Commands\Payments\CheckTransactionStatus;
use App\Console\Commands\Repayments\NotifyFailedRepayment;
//use App\Console\Commands\Repayments\OkraDDM;
//use App\Console\Commands\OkraBalance;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\Repayments\Checker as RepaymentChecker;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        RepaymentChecker::class,
        CheckTargets::class,

        SendInstructions::class,
        UpdateInstructions::class,
        CancelInstructions::class,
        MandateChecker::class,
        SettleInvestors::class,
        InvoiceIssuer::class,

        PortFolioManagement::class,

        NotifyFailedRepayment::class,

        CheckTransactionStatus::class,

        FulFilLoans::class,

        VerifyBuffers::class,

        BirthdayMessages::class,
        PayMonthlyBills::class,
        PayWeeklyBills::class,
        SweepManagedLoans::class,
        SweepAutoSweepingLoans::class,
        SweepPenableLoans::class,
        MoveFundToWallet::class,
        PromissoryNoteInterest::class,
        PenaliseLoans::class,
        CheckForPenalablePlans::class,
        UpdateAdminStatsCache::class,
        //OkraDDM::class,
        //OkraBalance::class,
        BillStatistics::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // Scheduled jobs
        $schedule->command('admin:stats-cache-update')
                ->hourlyAt(10)
                ->before(function() {Log::info("CRON JOB : STARTING ADMIN CACHE UPDATE");})
                ->after(function() {Log::info("CRON JOB: FINISHING ADMIN CACHE UPDATE");});      

        $schedule->command('monthly:bills')
                ->monthly()
                ->before(function() {Log::info("CRON JOB : STARTING MONTHLY BILL PAYMENT");})
                ->after(function() {Log::info("CRON JOB: FINISHING MONTHLY BILL PAYMENT");}); 

        $schedule->command('weekly:bills')
                ->weekly()
                ->before(function() {Log::info("CRON JOB : STARTING WEEKLY BILL PAYMENT");})
                ->after(function() {Log::info("CRON JOB: FINISHING WEEKLY BILL PAYMENT");}); 

        $schedule->command('sweep:penalties')
                ->daily()
                ->before(function() {Log::info("CRON JOB : STARTING SWEEP LOANS ON PENALTY");})
                ->after(function() {Log::info("CRON JOB: FINISHING SWEEP LOANS ON PENALTY");}); 
        
        $schedule->command('sweep:managed')
                ->dailyAt('12:00')
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING SWEEP LOANS ON MANAGED");})
                ->after(function() {Log::info("CRON JOB: FINISHING SWEEP LOANS ON MANAGED");}); 

        $schedule->command('penalise:loans')
                ->monthly()
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING PENALISING LOANS");})
                ->after(function() {Log::info("CRON JOB: FINISHING PENALISING LOANS");}); 
        

        $schedule->command('plans:penalable')
                ->daily()
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING CHECKING PENALABLE PLANS");})
                ->after(function() {Log::info("CRON JOB: FINISHING CHECKING PENALABLE PLANS");}); 

        $schedule->command('sweep:auto-loan')
                ->hourly()
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING AUTO LOAN SWEEP");})
                ->after(function() {Log::info("CRON JOB: FINISHING AUTO LOAN SWEEP");}); 

        $schedule->command('promissory:interest')
                ->dailyAt('12:00')
                ->before(function() {Log::info("CRON JOB : STARTING PROMISSORY NOTE INTEREST ACCRUAL");})
                ->after(function() {Log::info("CRON JOB: FINISHING PROMISSORY NOTE INTEREST ACCRUAL");}); 
        
        $schedule->command('remita:sendInstructions')
                ->dailyAt('11:00')
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING REMITA SEND INSTRUCTIONS");})
                ->after(function() {Log::info("CRON JOB: FINISHING REMITA SEND INSTRUCTIONS");}); 

        $schedule->command('target:check')
                ->dailyAt('07:00')
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING AFFILIATE TARGET CHECK");})
                ->after(function() {Log::info("CRON JOB: FINISHING AFFILIATE TARGET CHECK");}); 
        
        $schedule->command('investors:move_funds')
                ->daily()
                ->before(function() {Log::info("CRON JOB : STARTING INVESTORS FUND MOVE");})
                ->after(function() {Log::info("CRON JOB: FINISHING INVESTORS FUND MOVE");}); 
        
        $schedule->command('remita:updateInstructions')
                ->dailyAt('13:00')
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING UPDATING REMITA INSTRUCTIONS");})
                ->after(function() {Log::info("CRON JOB: FINISHING UPDATING REMITA INSTRUCTIONS");}); 
                
        $schedule->command('remita:cancelInstructions')
                ->twiceDaily(1, 14)
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING CANCEL REMITA INSTRUCTIONS");})
                ->after(function() {Log::info("CRON JOB: FINISHING CANCEL REMITA INSTRUCTIONS");}); 

        $schedule->command('remita:checkDdmMandates')
                ->dailyAt('10:00')
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING CHECKING DDM MANDATES");})
                ->after(function() {Log::info("CRON JOB: FINISHING CHECKING DDM MANDATES");}); 

        // $schedule->command('charge:portfolio')
        //         ->monthly();    

        $schedule->command('birthday:messages')
                ->daily()
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING BIRTHDAY MESSAGES");})
                ->after(function() {Log::info("CRON JOB: FINISHING BIRTHDAY MESSAGES");}); 

        // pays investors that suscribe to the backend payback cycle
        $schedule->command('investors:settle')
                ->hourly()
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING BACKEND INVESTORS SETTLE");})
                ->after(function() {Log::info("CRON JOB: FINISHING BACKEND INVESTORS SETTLE");}); 
        
        // $schedule->command('okra:ddm')
        //         ->hourly()
        //         ->withoutOverlapping()
        //         ->before(function() {Log::info("CRON JOB : STARTING OKRA DDM");})
        //         ->after(function() {Log::info("CRON JOB: FINISHING OKRA DDM");});
        
        // $schedule->command('okra:balance')
        //         ->hourly()
        //         ->withoutOverlapping()
        //         ->before(function() {Log::info("CRON JOB : RETRIEVING OKRA BALANCE");})
        //         ->after(function() {Log::info("CRON JOB: FINISH RETRIEVING OKRA BALANCE");});
        


        // pays investors that suscribe to the quarterly payback cycle
        $schedule->command('investors:settle --mode=quarterly')
                ->quarterly()
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING QUARTERLY INVESTORS SETTLE");})
                ->after(function() {Log::info("CRON JOB: FINISHING QUARTERLY INVESTORS SETTLE");}); 

        // pays investors that subscribe to the monthly payback cycle
        $schedule->command('investors:settle --mode=monthly')
                ->monthly()
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING MONTHLY INVESTORS SETTLE");})
                ->after(function() {Log::info("CRON JOB: FINISHING MONTHLY INVESTORS SETTLE");}); 

        $schedule->command('push:fulfill')
                ->everyFiveMinutes()
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING PUSHING LOANS FULFIL");})
                ->after(function() {Log::info("CRON JOB: FINISHING PUSHING LOANS FULFIL");}); 
        
        $schedule->command('bill:stats')
                ->dailyAt('08:00')
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING BILL STATS COMPUTE");})
                ->after(function() {Log::info("CRON JOB: FINISHING BILL STATS COMPUTE");}); 

        $schedule->command('notify:failed')
                ->dailyAt('23:45')
                ->before(function() {Log::info("CRON JOB : STARTING NOTIFICATION FOR FAILED REPAYMENTS");})
                ->after(function() {Log::info("CRON JOB: FINISHING NOTIFICATION FOR FAILED REPAYMENTS");}); 

        $schedule->command('check:transaction')
                ->everyTenMinutes()
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING CHECKING TRANSACTION");})
                ->after(function() {Log::info("CRON JOB: FINISHING CHECKING TRANSACTION");}); 
        
                
        $schedule->command('issue:invoices')
                ->dailyAt('08:00')
                ->withoutOverlapping()
                ->before(function() {Log::info("CRON JOB : STARTING ISSUEING INVOICES");})
                ->after(function() {Log::info("CRON JOB: FINISHING ISSUEING INVOICES");}); 
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
