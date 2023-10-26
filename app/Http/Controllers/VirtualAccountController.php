<?php

namespace App\Http\Controllers;

use App\Models\Investor;
use App\Models\User;
use App\Models\VirtualAccount as ModelsVirtualAccount;
use App\Models\WalletTransaction;
use App\Traits\VirtualAccount;
use App\VirtualAccount as AppVirtualAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VirtualAccountController extends Controller
{
    use VirtualAccount;
    public function create()
    {
        $user = Auth::user();
        // dd(getenv('APP_NAME'));
        $createPaystackCustomer = json_decode($this->createCustomer($user));

        if (!$createPaystackCustomer->status)
            return back()->with('failure', $createPaystackCustomer->message . ' from create customer');


    $this->validateCustomer($createPaystackCustomer->data->customer_code);
    // dd($validateCustomer->status);

    // if ($validateCustomer == null)
    //     return back()->with('failure', 'Failed to validate customer');

       

        $getCustomer = [
            'customer' => $createPaystackCustomer->data->customer_code,
            'preferred_bank' => env('APP_ENV') == 'local' ? 'test-bank' : 'wema-bank',
        ];

        

        // dd($this->createVirtualAccount($getCustomer));

        $createVirtualAccount = json_decode($this->createVirtualAccount($getCustomer));
        if (!$createVirtualAccount->status)
            return back()->with('failure', $createVirtualAccount->message);

        // dd($createVirtualAccount);


        ModelsVirtualAccount::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'customer_code' => $createVirtualAccount->data->customer->customer_code,
            'number' => $createVirtualAccount->data->account_number,
            'name' => $createVirtualAccount->data->account_name,
            'bank' => $createVirtualAccount->data->bank->name,
            'currency' => $createVirtualAccount->data->currency,
            'status' => 'active'
        ]);

        return back()->with('success', 'Virtual account created successfully');

    }


    public function webhook()
    {
        if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || !array_key_exists('x-paystack-signature', $_SERVER))
            // exit();
            // Retrieve the request's body
            $input = @file_get_contents("php://input");
        define('PAYSTACK_SECRET_KEY', env('PAYSTACK_SECRET_KEY'));

        // validate event do all at once to avoid timing attack
        if ($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY))
            // exit();

            http_response_code(200);

        // parse event (which is json string) as object
        // Do something - that will not take long - with $event
        $resEvent = json_decode($input);

        Log::info($input);


        if ($resEvent->event === 'charge.failed') {
            $reference = $resEvent->data->reference; //'QvnDDOsYW2hMZtdbCaTDwPXFnT';
            $status = $resEvent->data->status;
            $getAmount = $resEvent->data->amount / 100;
            $amount = $getAmount - $this->transaction_fee();
            $email = $resEvent->data->customer->email;
            $customer = $resEvent->data->customer->customer_code;

            $walletTransaction = WalletTransaction::where('reference', $reference)->first();
            $walletTransaction->status = 'failed';
            $walletTransaction->save();
            if (!$walletTransaction->exists()) {

                Log::info('Create failed transaction here');
                $user = User::where('email', $email)->first();
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'email' => $email,
                    'amount' => $amount,
                    'reference' => $reference,
                    'type' => 'deposit',
                    'description' => 'Wallet topup',
                    'payment_method' => $resEvent->data->channel,
                    'status' => 'failed'
                ]);
            }

 
        }

        if ($resEvent->event === 'charge.success') {
            $reference = $resEvent->data->reference; //'QvnDDOsYW2hMZtdbCaTDwPXFnT';
            $status = $resEvent->data->status;
            $getAmount = $resEvent->data->amount / 100;
            $email = $resEvent->data->customer->email;
            $customer = $resEvent->data->customer->customer_code;

            $walletTransaction = WalletTransaction::where('reference', $reference);

            if ($resEvent->data->channel == 'dedicated_nuban') {
                if ($walletTransaction->exists()) {
                    Log::info('TRansaction reference already exist ' . $reference);
                } else {
                    $va = ModelsVirtualAccount::where('email', $email)->first();
                    $model = ($va->model == 'user') ? 'App\Models\User' : 'App\Models\Investor';

                    Log::info('Create transaction here');
                    $user = ($va->model == 'user')
                        ? User::where('email', $email)->first()
                        : Investor::where('email', $email)->first();

            

                    WalletTransaction::create([
                        'owner_id' => $user->id,
                        'amount' => $getAmount,
                        'parties' => 1,
                        'owner_type' => $model,
                        'code' => 000,
                        'description' => 'Virtual account deposit',
                        'reference' => $reference,
                        'direction' => 1,
                        'purse' => 1,
                    ]);

                    $user->wallet += $getAmount;
                    $user->save();
                    exit();
                }
            }


        }
    }

}