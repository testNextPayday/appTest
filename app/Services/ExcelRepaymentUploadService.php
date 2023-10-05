<?php
namespace App\Services;

use App\Models\Employment;
use App\Services\ExcelUserFinder;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\LoanRepaymentService;
use App\Exports\SkippedRepaymentExport;

class ExcelRepaymentUploadService
{

    protected $repaymentService;

    public $source;

    protected $employerID;

    protected $skipped = 0; protected $uploaded = 0;

    protected $error;

    protected $rowFormat = ['computer_number', 'Name', 'Amount', 'MDA'];

    protected $skippedBasket = []; protected $skippedReasons = [];

    protected $rows;

    public function __construct()
    {
        $this->repaymentService = new LoanRepaymentService();
    }

    
    /**
     * Gets the status information of all uploads
     *
     * @return string
     */
    public function getUploadStatus()
    {
        $uploaded = $this->uploaded;
        $skipped = $this->skipped;
        $status =  "Upload Complete. ($uploaded Sent for Upload, $skipped Skipped)";
        if ($this->skipped > 0) {
            session()->flash('skippedUrl', "Download the skipped repayment sheet");
            $status .= 'Download Skips';
        }
        return $status;
    }

    
    /**
     * Set the excel upload employer
     *
     * @param  int $employerID
     * @return void
     */
    public function setUploadEmployer($employerID)
    {
        $this->employerID = $employerID;
    }

    
    /**
     * Get skipped repayments
     *
     * @return void
     */
    public function getSkips()
    {
        $skippedBasket = $this->skippedBasket;
        $skippedReasons = $this->skippedReasons;

        if (count($skippedBasket) > 0) {
           
            $skippedRows = $this->rows->filter(function ($value, $index) use ($skippedBasket) {
                return in_array($index, $skippedBasket);
            });
            
            // add reasons to this collection
            $skippedRows->each(function ($value, $index) use ($skippedReasons) {
                $value[] = $skippedReasons[$index];
            });

            $skippedRows = $skippedRows->prepend(['computer_number', 'Name', 'Amount', 'MDA', 'Reasons']);

            $stored = Excel::store(new SkippedRepaymentExport($skippedRows), 'skippedRepayments.xls', 'public');
            if (!$stored) throw new \Exception('Could not create skipped repayments aborting operation');

        }
    }
    
    /**
     * Takes in all excel data rows
     *
     * @param  mixed $rows
     * @return void
     */
    public function processRows($rows) 
    {
        $this->rows = $rows;
        
        $bulkUploads = [];

        foreach($rows as $rowIndex=> $rowData) {

            $this->checkFormat($rowData, $rowIndex);

            if (!$this->checkIssetValues($rowData, $rowIndex) ) {
                $reason = 'Payroll Id or name is not found';
                $this->logSkipped($rowData, $rowIndex, $reason);
                continue;
            }

            $user = $this->getUser($rowData[0], $rowData[1]);

            if (!$user) {
                $reason = 'We could not find this user';
                $this->logSkipped($rowData, $rowIndex, $reason);
                continue;
            }

            $upload = $this->getUploadData($user, $rowData);
            $bulkUploads[] = $upload;
            $this->uploaded++;
           
        }
      
        $this->repaymentService->makeBulkUploads($bulkUploads);

    }
    
    /**
     * setUploadSource
     *
     * @param  string $source
     * @return void
     */
    public function setUploadSource($source)
    {
        $this->source  = $source;
    }
    
    
    /**
     * Checks that a data rows confirms to a particular format
     *
     * @param  mixed $data
     * @return void
     */
    protected function checkFormat($rowData, $rowIndex) 
    {
        $rowData = $rowData->toArray();
        if ($rowIndex === 0) {
            $sentFormat = array_map('strtolower', $rowData);
            $expectedFormat = array_map('strtolower', $this->rowFormat);
            
            if ($sentFormat !== $expectedFormat) {
                $this->error = "Headings must be in the format: " . implode(", ", $this->rowFormat);
                throw new \Exception($this->error);
            }
        }
        
    }

    
    /**
     * Checks if column valuies are set
     *
     * @param  mixed $data
     * @param  mixed $rowIndex
     * @return void
     */
    private function checkIssetValues($data, $rowIndex) 
    {
        $data = $data->toArray();
        if (!isset($data[0]) || !isset($data[1])) {
            return false;
        }
        return true;
    }

    
    /**
     *  Finds the user with a particular payroll ID
     *
     * @param  int $payrollID
     * @return void
     */
    protected function getUser($payrollID, $userName)
    {
        $employerID = $this->employerID;
        $emp = (new ExcelUserFinder($payrollID, $userName, $employerID))->findUser();
        $user = $emp->user ?? null;
        return $user;
    }

    
    /**
     * Get Upload Data
     *
     * @param  mixed $user
     * @param  mixed $data
     * @return void
     */
    protected function getUploadData($user, $data)
    {
        
        return  [
            'borrower'=> $user->id,
            'paid_amount'=> $data[2],
            'collection_date'=> now(),
            'payment_method'=> $this->source ?? 'DDAS',
            'remarks'=> 'Excel Upload'
        ];
    }

    
    /**
     * Couldnt find a record for a particular user
     *
     * @param  mixed $data
     * @param  mixed $rowIndex
     * @return void
     */
    protected function logSkipped($data, $rowIndex, $reason)
    {
        if ($rowIndex == 0) {
            // simply do not log
            return false;
        }
        else  {

            $data = $data->toArray();

            $this->skipped++;
            $this->skippedBasket[] = $rowIndex;
            $this->skippedReasons[$rowIndex] = $reason;
        }
        
    }






}