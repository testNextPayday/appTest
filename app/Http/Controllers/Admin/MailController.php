<?php

namespace App\Http\Controllers\Admin;
use Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\CustomMail;
use App\Models\Employment;
use App\Models\Employer;
use App\Models\Investor;
use App\Models\Loan;
use App\Models\Affiliate;
use App\Models\Staff;

class MailController extends Controller
{
    //

    public function investor()
    {
        return view('admin.mails.investor');
    }

    public function staff()
    {
        return view('admin.mails.staff');
    }

    public function borrower()
    {
        $employers = Employer::where('is_primary',false)->get();
        return view('admin.mails.borrower',compact('employers'));
    }

    public function affiliate()
    {
        return view('admin.mails.affiliate');
    }
    public function sendMail(){
        $user_group = request('user_group');
        if(request()->has('borrowers')){
           $users = $this->getBorrowers($user_group);
           // dd($users);
        }elseif(request()->has('investors')){
           $users = $this->getInvestors();
        }elseif(request()->has('affiliates')){
            $users = $this->getAffiliates();
        }elseif(request()->has('staffs')){
            $users = $this->getStaffs();
        }else{
            return redirect()->back();
        }

        $subject = request('mail_subject');
        $body = request('mail_content');
        $sender = request('senders_email');
        $emails = array_unique($users->pluck('email')->toArray());

        try {
           $mail = Mail::to($emails)->send(new CustomMail($body,$subject,$sender));
           return redirect()->back()->with('success','Mail sent Successfully'); 
        } catch (Exception $e) {
            return redirect()->back()->with('err',$e->getMessage());
        }
        

    }
    public function getInvestors(){
        $investors = Investor::all();
        return $investors;
    }
    public function getStaffs(){
        $staffs = Staff::all();
        return $staffs;
    }
    public function getAffiliates(){
        $affiliates = Affiliate::all();
        return $affiliates;
    }
    public function getBorrowers($user_group){
        $employers = Employment::where('employer_id',$user_group)->get();
        $users = $employers->map(function($employer){
            return $employer->user;
        });
        return $users;
       
    }
   
}
