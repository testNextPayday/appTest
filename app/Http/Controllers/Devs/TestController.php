<?php

namespace App\Http\Controllers\Devs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    //

    public function test()
    {
        $data = [
            'no_of_shares'=> 25,
            'reference'=> 'CC5-URTRUEAKQ',
            'name'=> 'Test ',
            'address'=> 'No. 25 Achineke Wonodi Street ',
            'value_per_share'=> 5,
            'membership_date'=>'2020/05/05'
        ];
        return view('pdfs.hilcop_certificate', $data);
    }
}
