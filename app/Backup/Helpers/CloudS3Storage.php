<?php
namespace App\Backup\Helpers;

use Illuminate\Support\Facades\Storage;
use App\Backup\Helpers\StorageDestination;


class CloudS3Storage extends StorageDestination
{
    
    /**
     * The underlying storage engine
     *
     * @var mixed
     */
    protected $storageEngine;

    protected $disk;

        
    /**
     * Initialize the storage engine for the instance
     *
     * @return void
     */
    public function __construct()
    {

        $this->storageEngine = Storage::disk('backups_s3');

        $this->url = env('AWS_BACKUP_URL');

        $this->disk = 'backups_s3';
    }


   
}