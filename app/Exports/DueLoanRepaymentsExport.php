<?php

namespace App\Exports;

use Illuminate\Support\Collection;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DueLoanRepaymentsExport implements FromCollection, WithHeadings
{
    protected $type;
    
    protected $startDate;
    
    protected $endDate;
    
    public function __construct($type, $startDate = null, $endDate = null)
    {
        $this->type = $type;    
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $loans = $this->getLoansByType();
        
        return $this->extractData($loans);
    }
    
    /**
     * Returns loans that falls under a specified type
     * 
     * @return Illuminate\Support\Collection
     */
    private function getLoansByType()
    {
        $loanQuery = Loan::query();
        
        switch($this->type) {
            case "ippis":
                $loanQuery = $loanQuery->unfulfilled()->dasIPPIS()->approved();
                break;
        }
        
        return $loanQuery->get();
    }
    
    /**
     * Extracts the data we need for the export
     * 
     * @param Illuminate\Support\Collection $loans
     * @return Illuminate\Support\Collection
     */
    private function extractData(Collection $loans)
    {
        $data = [];
        
        $startDate = $this->startDate;
        
        $endDate = $this->endDate;
        
        $loans->each(function($loan, $key) use (&$data, $startDate, $endDate) {
            
            $duePayments = $loan->repaymentPlans()->due();
            
            if ($startDate)
                $duePayments = $duePayments->whereDate('payday', '>=', $startDate);
            
            if ($endDate)
                $duePayments = $duePayments->whereDate('payday', '<=', $endDate);
            
            $duePayments = $duePayments->get();
            
            $duePayments->each(function($payment, $key) use (&$data, $loan) {
                
                $borrower = $loan->user;
                
                $employment = $loan->loanRequest->employment;
                
                $employer = optional($employment)->employer;
                
                array_push($data, [
                    'name' => $borrower->name,
                    'email' => $borrower->email,
                    'employer' => optional($employer)->name ?? 'N/A',
                    'payroll ID' => optional($employment)->payroll_id ?? 'N/A',
                    'loan ID' => $loan->reference,
                    'amount' => number_format($payment->emi + $payment->management_fee, 2),
                    'payment number' => $payment->month_no,
                    'due date' => $payment->payday->toDateString(),
                ]); 
            });
        });
        
        return collect($data);
    }
    
    
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Employer',
            'Payroll ID',
            'Loan ID',
            'Amount',
            'Payment Number',
            'Due Date'
        ];
    }
}
