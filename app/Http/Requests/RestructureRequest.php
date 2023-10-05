<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestructureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'duration'=>'required',
            'charge'=>'numeric'
        ];
    }

    public function messages()
    {
        return [
            'loan.required'=> 'Provide a loan for Restructuring',
            'duration.required'=>'Provide an extended duration for loan Restructuring',
            'charge.numeric'=>'Charge must be a number'
        ];
    }
}
