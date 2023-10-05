<?php 
namespace App\Backup\Helpers;

use Carbon\Carbon;
use App\Models\Backup;
use App\Backup\Helpers\CleanUpStrategy;



class DefaultCleanupStrategy implements CleanUpStrategy

{
    
    /**
     * Provides config
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = config('backup.cleanup.default_strategy');
    
    }
    
    /**
     * Daily cleanup
     *
     * @return void
     */
    public function getReadyDaily()
    {
        $deleteableBackups = collect([]);

        $maxDays = $this->config['keep_daily_backups_for_days'];

        $today = Carbon::now();

        $dailyBackups = Backup::daily()->get();

        foreach ($dailyBackups as $backup) {

            $daysPast = $backup->created_at->diffInDays($today);

            if ($daysPast >= $maxDays) {
                $deleteableBackups->push($backup);
            }
            
            
        }

        return $deleteableBackups;

    }

    
    /**
     * Weekly cleanup
     *
     * @return void
     */
    public function getReadyWeekly()
    {
        $deleteableBackups = collect([]);

        $maxDays = $this->config['keep_weekly_backups_for_weeks'];

        $today = Carbon::now();

        $weeklyBackups = Backup::weekly()->get();

        foreach ($weeklyBackups as $backup) {

            $daysPast = $backup->created_at->diffInWeeks($today);

            if ($daysPast >= $maxDays) {
                $deleteableBackups->push($backup);
            }
            
            
        }

        return $deleteableBackups;
    }

    
    /**
     * Monthly Cleanup
     *
     * @return void
     */
    public function getReadyMonthly()
    {
        $deleteableBackups = collect([]);

        $maxDays = $this->config['keep_monthly_backups_for_months'];

        $today = Carbon::now();

        $monthlyBackups = Backup::monthly()->get();

        foreach ($monthlyBackups as $backup) {

            $daysPast = $backup->created_at->diffInMonths($today);

            if ($daysPast >= $maxDays) {
                $deleteableBackups->push($backup);
            }
            
            
        }

        return $deleteableBackups;
    }
    
    /**
     * Yearly Cleanup
     *
     * @return void
     */
    public function getReadyYearly()
    {
        $deleteableBackups = collect([]);

        $maxDays = $this->config['keep_yearly_backups_for_years'];

        $today = Carbon::now();

        $yearlyBackups = Backup::yearly()->get();

        foreach ($yearlyBackups as $backup) {

            $daysPast = $backup->created_at->diffInYears($today);

            if ($daysPast >= $maxDays) {
                $deleteableBackups->push($backup);
            }
            
            
        }

        return $deleteableBackups;
    }
}