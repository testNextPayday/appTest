<?php
namespace App\Services;

use App\Backup\Tasks\Backup;
use App\Backup\Tasks\Cleanup;
use App\Backup\Helpers\CloudS3Storage;
use Illuminate\Support\Facades\Storage;
use App\Backup\Helpers\DefaultCleanupStrategy;
use DB;


/**
 * Backup Class responsible for backing up our data
 */
class BackupService 
{


    protected $backupJob;


    protected $cleanupJob;
    
    /**
     * Initializa the backup
     *
     * @param  mixed $backup
     * @return void
     */
    public function __construct(Backup $backup)
    {

        $this->backupJob = $backup;
    }

    
    /**
     * Runs the backup task
     *
     * @return void
     */
    public function runBackup()
    {
        try {

            DB::beginTransaction();

            $this->backupJob->setDestination(new CloudS3Storage);
        
            $this->backupJob->dumpDatabase();

            DB::commit();

        }catch(\Exception $e) {

            DB::rollback();
            dd($e->getMessage());
        }
       
       
    }

    
    /**
     * Cleans up outdated backups
     *
     * @return void
     */
    public function runCleanup()
    {
        $this->cleanupJob = new Cleanup(new DefaultCleanupStrategy);
        
        $this->cleanupJob->cleanBackups();
    }

    
    /**
     * Sets the backup type on the backup job
     *
     * @param  mixed $type
     * @return void
     */
    public function setBackupType($type)
    {
        $this->backupJob->setBackupType($type);
    }


}