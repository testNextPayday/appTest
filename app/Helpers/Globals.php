<?php

use Illuminate\Support\Str;

if (!function_exists('str_plural')) {
    function str_plural(string $value, $count = 2)
    {
        Str::plural($value, $count);
    }
}

if (!function_exists('AES128CBC_encrypt')) {

   function AES128CBC_encrypt($data, $iv, $key)
    {
        $cipherText = trim(base64_encode(openssl_encrypt($data, 'AES-128-CBC', $key, true, $iv)));
        unset($data, $iv, $key);
        return $cipherText;
    }
    
}

if (! function_exists('paystack_charge')) {

    function paystack_charge ($amount) 
    {
        $charge = (1.5 / 100) * $amount;

        if ($amount > 2500) {

            $charge += 100;
        }

        return round($charge, 2);
    }
}

if (! function_exists('fee_charge')) {

    function fee_charge($amount, $feePercent) {

        $percentage = $feePercent/100;
        $num = 1 - $percentage;
        $loanAmount = $amount/$num;
        $charge = $loanAmount - $amount;
        return $charge;
    }
}

if (!function_exists('setBufferBatch')) {
    
    /**
     * We get the batch and store it in the session
     *
     * @return int
     */
    function setBufferBatch()
    {
        $maxBatch = \App\Models\PaymentBuffer::all()->max('batch') ?? 0;

        $batch = $maxBatch + 1;

        // store in the session
        session()->put('batch', $batch);

        return $batch;
    }
}




if (! function_exists('pmt')) {
    
    /**
     * A global version of the pmt func
     *
     * @param  mixed $amount
     * @param  mixed $rate
     * @param  mixed $tenure
     * @return void
     */
    function pmt($amount,$rate,$tenure)
    {
       
        $rate = round(($rate)/100, 5);
        $a = $amount * $rate;
        $b = 1 -  pow((1 + $rate), - $tenure);
        return $a/$b;
    }
}

if (! function_exists('str_lreplace')) {
    
    /**
     * str_lreplace
     *
     * @param  mixed $search
     * @param  mixed $replace
     * @param  mixed $subject
     * @return void
     */
    function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);
    
        if ($pos !== false)
        {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }
    
        return $subject;
    }
}


// used by the moneygram and paystack money sender classes
if (! function_exists('validateFields')) {

    function validateFields($array,...$args) {

        if (! is_array($array)) {
            throw new \InvalidArgumentException(
                "Array argument expected but ".
                get_class($array)
                ." was given to validateFields"
            );
        }
        foreach ($args as $arg) {
            
            if (!array_key_exists($arg, $array) || is_null($array[$arg])) {

                throw new \InvalidArgumentException(
                    "$arg is required to complete the request"
                );

            }

           
        }
    }


    if(! function_exists('generateHash')){

        function generateHash(){

           return substr(hash('sha256', mt_rand(20,25) . microtime()), 0, 20);
        }
    }


    if(! function_exists('fileReader')){

        function fileReader($path){

            $fullPath = base_path($path);

            if(file_exists($fullPath)){

                return require $fullPath;
            }

            throw new \Illuminate\Contracts\Filesystem\FileNotFoundException(" The file to read from does not exists");

        }
    }

    if(! function_exists('make_smsable')){

        function make_smsable($number)
        {
            return "234" . substr(str_replace(" ", "", trim($number)), -10);
        }
    }
}
?>