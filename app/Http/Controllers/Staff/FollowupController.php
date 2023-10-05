<?php

namespace App\Http\Controllers\Staff;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Investor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FollowupController extends Controller
{
    //

    
    /**
     * Get all the investors that have never funded a loan request
     *
     * @return void
     */
    public function investors()
    {
        $investors = Investor::with('loanFunds')->get();

        $neverFundedLoans = $investors->filter(function($investor) {
            return $investor->loanFunds->count() == 0 && date('Y',strtotime($investor->created_at)) >=  '2020';
        });

        return view('staff.followup.investors', ['investors'=> $neverFundedLoans]);
    }

    
    /**
     * Take up 
     *
     * @param  mixed $request
     * @param  mixed $user
     * @return void
     */
    public function takeUp(Request $request, User $user)
    {
       try {

            $staff = auth('staff')->user();

            if ($staff) {

                $user->update(
                    [
                        'adder_type'=> get_class($staff),
                        'adder_id'=> $staff->id,
                        'taken_up'=> 1
                    ]
                );

            }
       }catch(\Exception $e) {

           return redirect()->back()->with('failure', $e->getMessage());
       }

       return redirect()->back()->with('success', 'Borrower marked as yours');
       
    }


    
    /**
     * Get All users that never made a request
     *
     * @return void
     */
    public function users()
    {
        $users = User::with('loanRequests')->get();

        $neverRequestedLoan = $users->filter(function($user) {
            return $user->loanRequests->count() == 0 && date('Y',strtotime($user->created_at)) >=  '2020';
        });

        $greaterThan2Hours = $neverRequestedLoan->filter(function($user) {
            $now = Carbon::now();
            return $user->created_at->diffInMinutes($now) > 15;
        });
        
        return view('staff.followup.users', ['users'=> $greaterThan2Hours]);
    }


    
}
