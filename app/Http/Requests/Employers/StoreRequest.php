<?php

namespace App\Http\Requests\Employers;

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
            auth('affiliate')->check() || 
            auth('admin')->check())
            
            return true;
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'phone' => 'required|string', 
            'address' => 'required|string',
            'state' => 'required|string',
            'email' => 'required|email|max:255',
            'payment_date' => 'required', 
            'payment_mode' => 'required', 
            'approver_name' => 'required|string',
            'approver_email' => 'required|email',
            'approver_designation' => 'required|string',
            'approver_phone' => 'required'
        ];
    }
}
