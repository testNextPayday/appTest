<?php
namespace App\Backup\Helpers;




class DumpFailed extends  \Exception
{

    
    /**
     * Process failed
     *
     * @param  mixed $process
     * @return void
     */
    public static function processFailed($process) {

        return new static("The dump process failed with exitcode {$process->getExitCode()} : {$process->getExitCodeText()} : {$process->getErrorOutput()}");
    }


    
    /**
     * File was not created
     *
     * @return void
     */
    public static function failedToCreateFile()
    {
        return new static('The dumpfile could not be created');
    }

    
    /**
     * Dump File is empty
     *
     * @return void
     */
    public static function dumpFileWasEmpty()
    {
        return new static('The dump file was 0 KB ');
    } 
}
