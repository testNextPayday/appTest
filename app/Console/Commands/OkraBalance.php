<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use App\Models\User;
use App\Models\BankDetail;
use App\Services\Okra\OkraService;
use Illuminate\Console\Command;

class OkraBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'okra:balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve and Update Okra balance ID in user bank details record';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $okraService = new OkraService(new Client);
        $users = User::with('banks')->get()->filter(function($users){ return isset($users->banks->last()->okra_account_id) ;});
        
        foreach($users as $user){
            $bank = $user->banks->last();
            if(!$bank->okra_balance_id){
                $accountId = $bank->okra_account_id;
                $okraService->getBankAccountDetails($accountId);
                $accountInfo = $okraService->getResponse();
                $balanceID = $accountInfo['data']['accounts'][0]['balance'];
                $bank->update(['okra_balance_id'=> $balanceID]);
                
            }
            
        }
    }

    
}
