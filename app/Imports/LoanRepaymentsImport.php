<?php

namespace App\Imports;

use App\Models\Loan;
use App\Models\Employment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SkippedRepaymentExport;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Services\ExcelRepaymentUploadService;

class LoanRepaymentsImport implements ToCollection
{
    protected $source;

    protected $uploadService;

    protected $employerID;

    public function __construct($type, $employerID)
    {
        $this->source = $type;
        $this->employerID = $employerID;
        $this->uploadService = new ExcelRepaymentUploadService();
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {

        $this->uploadService->setUploadEmployer($this->employerID);

        $this->uploadService->processRows($rows);

        $this->uploadService->setUploadSource($this->source);

        $uploadService = $this->uploadService;

        // Bind this upload service class to the service container
        App::singleton(ExcelRepaymentUploadService::class, function() use ($uploadService){ return $uploadService; });
    }


  
}
