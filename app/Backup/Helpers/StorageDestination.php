<?php
namespace App\Backup\Helpers;
use DB;
use Carbon\Carbon;
use App\Models\Backup;

/**
 *  Storage destination of the backup
 */
abstract class StorageDestination
{

    protected $storageEngine;
    
    /**
     * 
     *
     * @param  mixed $folder
     * @param  mixed $contents
     * @param  mixed $type
     * @return void
     */
    public function put($folder, $contents, $type)
    {
        $folderArray = explode('/', $folder);
        $filename = $folderArray[array_key_last($folderArray)];

        $this->storageEngine->put($folder, $contents);

        $this->storeDB($filename, $type, $folder);
    }
    
    /**
     * Stores a created backup on the database
     *
     * @param  mixed $folder
     * @param  mixed $filename
     * @return void
     */
    protected function storeDB($filename, $type, $folder)
    {
        $url = $this->url.'/'.$folder;

        Backup::create([
            'url'=>$url,
            'name'=>$filename,
            'backup_frequency'=> $type,
            'storage'=> $this->disk
        ]);
    }


}