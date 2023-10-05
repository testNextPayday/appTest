<?php
namespace App\Backup\Tasks;

use App\Backup\Helpers\CleanUpStrategy;
use Illuminate\Support\Facades\Storage;

/**
 *  Class responsible for clearing old backups
 * 
 */
class Cleanup
{
    
    /**
     * A strategy for cleaning
     *
     * @var mixed
     */
    protected $strategy;

    
    /**
     * Array of backups to be cleaned up
     *
     * @var array
     */
    protected $cleanupList;
    
    /**
     * Initialises a clean up strategy
     *
     * @param  mixed $strategy
     * @return void
     */
    public function __construct(CleanUpStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    
    /**
     * Clean up old backups
     *
     * @return void
     */
    public function cleanBackups()
    {
        $this->cleanupList = collect([]);

        $this->prepareOldBackups();

        $this->deleteOldBackups();
    }

    
    /**
     * Clean Backups
     *
     * @return void
     */
    protected function prepareOldBackups()
    {
        $list = collect([]);

        $list = $list->merge($this->strategy->getReadyDaily());

        $list = $list->merge($this->strategy->getReadyWeekly());
       
        $list = $list->merge($this->strategy->getReadyMonthly());

        $list = $list->merge($this->strategy->getReadyYearly());

        $this->cleanupList = $list;
    }

    
    /**
     * Delete old backups from the storage destination
     *
     * @return void
     */
    protected function  deleteOldBackups()
    {

       
        if ($this->cleanupList->count() > 0) {
          
            foreach ($this->cleanupList as $backup) {
           
                $disk = $backup->storage;
                $relativeUrl = $this->getRelativeUrl($backup->url);
                
                $deleteResponse = Storage::disk($disk)->delete($relativeUrl);
               
                if ($deleteResponse) {
                    
                    $backup->forceDelete();
                }

               
            }
        }
       
      
    }

    
    /**
     * Retrieve relative url from full Url provided
     *
     * @param  string $url
     * @return string
     */
    protected function getRelativeUrl($url) 
    {
        $fullPath = $url;
        $mainPath = getenv('AWS_BACKUP_URL');

        $relativeUrl = trim(str_replace($mainPath, " ", $fullPath));

        return $relativeUrl;
    }


}