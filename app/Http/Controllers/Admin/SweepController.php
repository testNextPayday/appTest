<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Loan;
use App\Models\Employer;
use App\Models\Bucket;
use App\Models\User;

use App\Unicredit\Collection\Utilities;
use App\Unicredit\Collection\LoanSweeper;
class SweepController extends Controller
{
    private $sweeper;
    
    public function __construct(LoanSweeper $sweeper)
    {
        $this->sweeper = $sweeper;    
    }
    
    public function sweepEmployerLoans(Employer $employer)
    {
        $employeeLoans = $employer->getEmployeeUnfulfilledLoans();
        $this->sweeper->sweep($employeeLoans);
        return redirect()->back()->with('success', "Sweep performed on {$employer->name}'s Loans");
    }
    
    public function sweepBorrowerAccount(User $user)
    {
        $userLoans = $user->receivedLoans()->unfulfilled()->get();
        $this->sweeper->sweep($userLoans);
        return redirect()->back()->with('success', "Sweep performed on {$user->name}'s Loans");
    }
    
    public function sweepBucket(Bucket $bucket)
    {
        $employers = $bucket->employers;
        
        foreach($employers as $employer) {
            $employeeLoans = $employer->getEmployeeUnfulfilledLoans();
            $this->sweeper->sweep($employeeLoans);    
        }
        
        return redirect()->back()->with('success', "Sweep performed on {$bucket->name}");
    }
}
