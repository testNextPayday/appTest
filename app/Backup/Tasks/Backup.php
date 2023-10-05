<?php
namespace App\Backup\Tasks;

use App\Backup\Helpers\DbDumper;
use App\Backup\Helpers\Compressor;
use App\Backup\Helpers\DumpFailed;
use Symfony\Component\Process\Process;
use App\Backup\Helpers\StorageDestination;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;


class Backup 
{
    
    protected const FILE_FORMAT = 'd_M_Y';

    /**
     * Time in seconds for the process to timeout
     */
    protected const TIME_OUT = 3600;

    /**
     * 
     *
     * @var \App\Backup\Helpers\DbDumper
     */
    protected $dumper;
    
    /**
     * s
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $storageDestination;


    protected $type = 'daily';


    protected $filename;


    
    /**
     * Initialize
     *
     * @param  mixed $dumper
     * @return void
     */
    public function __construct(DbDumper $dumper)
    {
        $this->dumper = $dumper;
    }

    
    /**
     * Run the database dump operations
     *
     * @return void
     */
    public function dumpDatabase()
    {
        $dir = '';

        $this->dumper->setTemporaryDirectory($dir);

        $filename = $this->dumper->getDateName();
        
        $command  = $this->dumper->getCommand();

        $this->filename = $this->dumper->getFileName();

        $this->execute($command);

        list($zipFile, $sqlFile) = $this->zipFile($dir, $filename);
        
        $type = $this->type;
        
        $folder = "backups/{$type}/{$filename}";
        $contents = file_get_contents($zipFile);
        $this->storageDestination->put($folder, $contents, $type);

        $this->destroyBackupFiles($zipFile, $sqlFile);

    }
    
    /**
     * Execute the database process
     *
     * @param  string $command
     * @return void
     */
    protected function execute($command)
    {
        
        $process  = new Process($command);

        $process->start();

        $process->setTimeOut(self::TIME_OUT);

        $process->wait();


        $this->checkIfDumpWasSuccessful($process);

        
    }

    
    /**
     * Check if the dump was successful
     *
     * @param  mixed $process
     * @return void
     */
    protected function checkIfDumpWasSuccessful($process)
    {
        if (! $process->isSuccessful()) {

            throw DumpFailed::processFailed($process);
        }

        if (! file_exists($this->filename)) {
            throw DumpFailed::failedToCreateFile();
        }


        if (filesize($this->filename) == 0) {
            throw DumpFailed::dumpFileWasEmpty();
        }



    }

    
    /**
     * Zips the SQL File before transfer to the server
     *
     * @param  string $dir
     * @param  string $filename
     * @return void
     */
    protected function zipFile($dir, $filename)
    {
        $sqlFile = $dir.$filename.'.sql';

        $zipFile = $dir.$filename.'.zip';

        if (! Compressor::zip($zipFile, $sqlFile)) {

            throw new \Exception('Unable to zip file');
        }

        return [$zipFile, $sqlFile];
    }


    
    /**
     * Storage Destination
     *
     * @param  mixed $storage
     * @return void
     */
    public function setDestination(StorageDestination $storage)
    {
        $this->storageDestination = $storage;
    }

    
    /**
     * Destroy all directories passed to it
     *
     * @param  mixed $args
     * @return void
     */
    protected function destroyBackupFiles(...$args)
    {
        foreach ($args as $file) {
    
            unlink($file);
        }
    }

    
    /**
     * Backup Type
     *
     * @param  string $type
     * @return void
     */
    public function setBackupType($type)
    {
        $this->type = $type;
    }




}