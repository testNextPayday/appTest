<?php
namespace App\Backup\Helpers;

use ZipArchive;


class Compressor
{
    
    /**
     * Zip File
     * @param string $zipFile - Name of the zip file
     * @param  mixed $filename -  Name of the file to zip
     * @return void
     */
    public static function zip($zipFile, $filename)
    {
        
        $zipper = new ZipArchive;

        $zipResponse = $zipper->open($zipFile, ZipArchive::CREATE);

        if ($zipResponse === TRUE) {

            $zipper->addFile($filename);

            $zipper->close();
        }

        return file_exists($zipFile);
    }

}