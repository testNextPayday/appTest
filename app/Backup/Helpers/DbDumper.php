<?php
namespace App\Backup\Helpers;

use Carbon\Carbon;

/** The DbDumper is responsible for generating the dump command 
 *  Implemented the chain of responsibility pattern as seen in setallparams
 */
class DbDumper 

{
    
    /**
     * The user of the DB
     *
     * @var string
     */
    protected $dbUser;
    
    /**
     * Name of the database to dump
     *
     * @var string
     */
    protected $dbName;
    
    /**
     * Database password
     *
     * @var string
     */
    protected $dbPassword;

    
    /**
     * Temporay file destination path
     *
     * @var string
     */
    protected $tempDestination;

    
    /**
     * Format of file date storage
     *
     * @var undefined
     */
    protected $fileFormat =  'd_M_Y_H_i_s';


    protected $filename;

    
    
    /**
     * Initilizes the set all params
     *
     * @return void
     */
    public function __construct()
    {
        $this->setAllParams();
    }

    
    /**
     * Sets all dumper params
     *
     * @return void
     */
    protected function setAllParams()
    {
        $this->setDbName()
            ->setDbUser()
            ->setPassword()
            ->setTemporaryDirectory();
    }
    
    /**
     * Sets the temporary directory where the file stays
     *
     * @param  mixed $directory
     * @return void
     */
    public function setTemporaryDirectory($directory = null) 
    {
        if (! $directory) {
            $directory = '';
        }

        $this->tempDestination = $directory;

        $this->filename = $this->tempDestination."{$this->getDateName()}.sql";

        return $this;
    }
    
    /**
     * Set Database Name
     *
     * @return $this
     */
    protected function setDbName()
    {
        $this->dbName = getenv('DB_DATABASE');

        return $this;
    }
    
    /**
     * Set Database User
     *
     * @return $this
     */
    protected function setDbUser()
    {
        $this->dbUser = getenv('DB_USERNAME');

        return $this;
    }
    
    /**
     * Set Database Password
     *
     * @return $this
     */
    protected function  setPassword()
    {
        $this->dbPassword = getenv('DB_PASSWORD');

        return $this;
    }
    
    /**
     * Gets the dump command for dumping files to temp
     *
     * @return string
     */
    public function getCommand()
    {       
        $command = "mysqldump -u {$this->dbUser} {$this->dbName} --password={$this->dbPassword} > {$this->filename}";
        return $command;
    }
    
    /**
     * Get File name
     *
     * @return void
     */
    public function getFileName()
    {
        return $this->filename;
    }

    
    /**
     * Returns date of file
     *
     * @return string
     */
    public function getDateName()
    {
        $today = Carbon::now()->format($this->fileFormat);

        return $today;
    }

}