<?php
namespace App\Backup\Helpers;


/**
 *  Cleanup Strategy for clearing old backups
 */

 interface CleanUpStrategy
 {

        
    /**
     * Gets daily backups ready for cleanup
     *
     * @return void
     */
    public function getReadyDaily();

    
    /**
     * Gets weekly backups ready for cleanup
     *
     * @return void
     */
    public function getReadyWeekly();

    
    /**
     * Gets monthly backups ready for cleanup
     *
     * @return void
     */
    public function getReadyMonthly();

    
    /**
     * Gets yearly backups ready for cleanup
     *
     * @return void
     */
    public function getReadyYearly();
 }
