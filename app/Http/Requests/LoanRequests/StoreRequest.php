<?php

namespace App\Http\Requests\LoanRequests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;


class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (auth('staff')->check() || 
            auth('affiliate')->check())
            
            return true;
            
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'amount' => 'required',
            'duration' => 'required|integer|min:1',
            //'comment' => 'required',
            // 'expected_withdrawal_date' => 'required|date',
            'reference' => 'required|exists:users',
            'bank_statement' => $request->bank_statement == null ? '' : 'required|file|mimes:jpeg,jpg,png,pdf|max:10240',
            // 'pay_slip' => $request->pay_slip == null ? '' : 'required|file|mimes:jpeg,jpg,png,pdf|max:1024'
        ];
    }
}
