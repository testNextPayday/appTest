<?php
namespace App\Traits;

use App\Models\BankDetail;
use Egulias\EmailValidator\EmailLexer;
use Illuminate\Support\Facades\Auth;

trait VirtualAccount
{
    public function createCustomer($user)
    {
        $url = "https://api.paystack.co/customer";

        // dd('234'.$user['phone']);
        $name = explode(' ', $user['name']);

        // dd($name[0]);

        $fields = [
            "email" => $user['email'],
            "first_name" => $name[0],
            "last_name" => $name[1],
            "phone" => '234' . $user['phone'],
        ];


        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . env('PAYSTACK_PUBLIC_KEY'),
            "Cache-Control: no-cache",
        )
        );

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        // dd(env('PAYSTACK_SECRET_KEY'));
        return $result;
    }

    public function createVirtualAccount($data)
    {

        $url = "https://api.paystack.co/dedicated_account";

        $fields = [
            "customer" => $data['customer'],
            "preferred_bank" => $data['preferred_bank']
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Cache-Control: no-cache",
        )
        );

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        return $result;


        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => "https://api.paystack.co/dedicated_account",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "POST",
        //     CURLOPT_POSTFIELDS => array("customer" => $data['customer'], "preferred_bank" => $data['preferred_bank']),
        //     CURLOPT_HTTPHEADER => array(
        //         "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
        //         "Content-Type: application/json"
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // return $response;
    }


    public function validateCustomer($customer_code)
    {
        $url = "https://api.paystack.co/customer/" .$customer_code. "/identification";

        $bankDetail = BankDetail::where('owner_id', Auth::id())->first();
        $name = explode(' ', Auth::user()->name);

        $fields = [
            "country" => "NG",
            "type" => "bank_account",
            "account_number" => $bankDetail->account_number, // "0123456789",
            "bvn" => Auth::user()->bvn , //"20012345677",
            "bank_code" => $bankDetail->bank_code, //"007",
            "first_name" => $name[0],
            "last_name" => $name[1]

        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Cache-Control: no-cache",
        )
        );

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        echo $result;
    }
}