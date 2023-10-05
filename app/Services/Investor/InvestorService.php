<?php
namespace App\Services\Investor;

use App\Models\Investor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Investors\AccountCreated as InvestorAccountCreated;

class InvestorService 
{

    public function createInvestor(Request $request)
    {

        $password = Str::random(8);

        $data = $request->all();
        $data['source_of_income'] = $request->source_of_income ?? 'Undisclosed';
        $data['password'] = bcrypt($password);
        $data['is_active'] = true;
        $data['avatar'] = 'public/defaults/avatars/default.png';
        $data['role'] = $request->role ?? 1;


        $investor = Investor::create($data);
        
        //send this password to the user
        if($investor) {
            $investor->notify(new InvestorAccountCreated($password));
        }

        return $investor;
    }
}